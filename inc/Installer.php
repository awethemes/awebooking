<?php
namespace AweBooking;

use WP_Roles;
use Psr\Log\LoggerInterface;
use AweBooking\Support\Collection;
use AweBooking\Bootstrap\Setup_Environment;
use AweBooking\Http\Async\Background_Updater;

class Installer {
	/**
	 * The AweBooing class instance.
	 *
	 * @var \AweBooking\AweBooking
	 */
	protected $awebooking;

	/**
	 * The background updater.
	 *
	 * @var \AweBooking\Controllers\Background_Updater
	 */
	protected $background_updater;

	/**
	 * DB updates and callbacks that need to be run per version.
	 *
	 * @var array
	 */
	protected $db_updates = [
		'3.0.0-beta10' => array(
			'awebooking_update_300_beta10_fix_db_types',
		),
		'3.0.0-beta12' => array(
			'awebooking_update_300_beta12_change_settings',
		),
		'3.0.0-beta15' => array(
			'awebooking_update_300_beta15_occupancy',
		),
	];

	/**
	 * Create the AweBooking installer.
	 *
	 * @param AweBooking $awebooking The AweBooing class instance.
	 */
	public function __construct( AweBooking $awebooking ) {
		$this->awebooking = $awebooking;
		$this->background_updater = $awebooking->make( Background_Updater::class );
	}


	/**
	 * Hooks in the WordPress.
	 *
	 * @return void
	 */
	public function init() {
		add_action( 'init', [ $this, 'maybe_reinstall' ], 5 );
		add_action( 'init', [ $this, 'register_metadata_table' ], 0 );
		add_filter( 'wpmu_drop_tables', [ $this, 'wpmu_drop_tables' ] );

		add_filter( 'plugin_row_meta', [ $this, 'plugin_row_meta' ], 10, 2 );
		add_filter( "plugin_action_links_{$this->awebooking->plugin_basename()}", [ $this, 'plugin_action_links' ] );
		add_action( 'after_plugin_row', [ $this, 'plugin_addon_notices' ], 10, 3 );
	}

	/**
	 * Doing action on the activation the awebooking.
	 *
	 * @return void
	 */
	public function activation() {
		if ( apply_filters( 'awebooking/enable_setup_wizard', $this->is_new_install() ) ) {
			set_transient( '_awebooking_activation_redirect', 1, 30 );
		}

		// Call the install action.
		$this->install();
	}

	/**
	 * Doing action on the deactivation the awebooking.
	 *
	 * @return void
	 */
	public function deactivation() {
		// Nothing todo for now.
	}

	/**
	 * Doing the install.
	 *
	 * @return void
	 */
	protected function install() {
		if ( ! is_blog_installed() ) {
			return;
		}

		// Check if we are not already running this routine.
		if ( 'yes' === get_transient( 'awebooking_installing' ) ) {
			return;
		}

		// If we made it till here nothing is running yet, let's set the transient now.
		set_transient( 'awebooking_installing', 'yes', MINUTE_IN_SECONDS * 10 );

		// Begin the installing...
		if ( ! defined( 'AWEBOOKING_INSTALLING' ) ) {
			define( 'AWEBOOKING_INSTALLING', true );
		}

		$this->setup_environment();
		$this->create_tables();
		$this->create_options();
		$this->create_roles();
		$this->create_cron_jobs();
		$this->update_version();

		// CHeck for the update DB.
		if ( $this->needs_db_update() ) {
			$this->update();
		} else {
			$this->update_db_version();
		}

		// Everything seem done, delete the transient.
		delete_transient( 'awebooking_installing' );

		// Fire installed action.
		do_action( 'awebooking/installed' );

		// Remove rewrite rules and then recreate rewrite rules.
		@flush_rewrite_rules();
	}

	/**
	 * Push all needed DB updates to the queue for processing.
	 *
	 * @return void
	 */
	protected function update() {
		$logger = $this->awebooking->make( LoggerInterface::class );

		$current_db_version = $this->get_current_db_version();
		$update_queued = false;

		foreach ( $this->db_updates as $version => $update_callbacks ) {
			// Ignore versions that older than current db version,
			// they may has been runned.
			if ( version_compare( $version, $current_db_version, '<' ) ) {
				continue;
			}

			// Loop through callbacks and add to queue.
			foreach ( $update_callbacks as $update_callback ) {
				$logger->info( sprintf( 'Queuing %1$s - %2$s', $version, $update_callback ), [ 'source' => 'db_updates' ] );

				$this->background_updater->push_to_queue( $update_callback );

				$update_queued = true;
			}
		}

		// Only dispatch when update_queued marked true.
		if ( $update_queued ) {
			$this->background_updater->save()->dispatch();
		}
	}

	/**
	 * Check AweBooking version and run the reinstall is required.
	 *
	 * This check is done on all requests and runs if the versions do not match.
	 *
	 * @access private
	 */
	public function maybe_reinstall() {
		if ( defined( 'IFRAME_REQUEST' ) || $this->get_current_version() === $this->awebooking->version() ) {
			return;
		}

		if ( ! defined( 'AWEBOOKING_REINSTALLING' ) ) {
			define( 'AWEBOOKING_REINSTALLING', true );
		}

		$this->install();

		do_action( 'awebooking/updated' );
	}

	/**
	 * Support awebooking tables and item-metadata.
	 */
	public function register_metadata_table() {
		global $wpdb;

		$wpdb->tables[] = 'awebooking_booking_itemmeta';
		$wpdb->booking_itemmeta = $wpdb->prefix . 'awebooking_booking_itemmeta';
	}

	/**
	 * Uninstall tables when MU blog is deleted.
	 *
	 * @access private
	 *
	 * @param  array $tables List the tables to be deleted.
	 * @return array
	 */
	public function wpmu_drop_tables( $tables ) {
		global $wpdb;

		$tables[] = $wpdb->prefix . 'awebooking_rooms';
		$tables[] = $wpdb->prefix . 'awebooking_booking';
		$tables[] = $wpdb->prefix . 'awebooking_pricing';
		$tables[] = $wpdb->prefix . 'awebooking_availability';
		$tables[] = $wpdb->prefix . 'awebooking_booking_items';
		$tables[] = $wpdb->prefix . 'awebooking_booking_itemmeta';

		return $tables;
	}

	/**
	 * Show action links on the plugin screen.
	 *
	 * @access private
	 *
	 * @param  mixed $links Plugin action links.
	 * @return array
	 */
	public function plugin_action_links( $links ) {
		$action_links = [
			'settings' => '<a href="' . admin_url( 'admin.php?page=awebooking-settings' ) . '" aria-label="' . esc_attr__( 'View AweBooking Settings', 'awebooking' ) . '">' . esc_html__( 'Settings', 'awebooking' ) . '</a>',
		];

		return array_merge( $action_links, $links );
	}

	/**
	 * Show row meta on the plugin screen.
	 *
	 * @access private
	 *
	 * @param  mixed $links Plugin row meta.
	 * @param  mixed $file  Plugin base file.
	 * @return array
	 */
	public function plugin_row_meta( $links, $file ) {
		if ( $this->awebooking->plugin_basename() !== $file ) {
			return (array) $links;
		}

		$row_meta = array(
			'docs'       => '<a href="' . esc_url( 'http://docs.awethemes.com/awebooking' ) . '" aria-label="' . esc_attr__( 'View documentation', 'awebooking' ) . '">' . esc_html__( 'Docs', 'awebooking' ) . '</a>',
			'demo'       => '<a href="' . esc_url( 'http://demo.awethemes.com/awebooking' ) . '" aria-label="' . esc_attr__( 'Visit demo', 'awebooking' ) . '">' . esc_html__( 'Demo', 'awebooking' ) . '</a>',
			'contribute' => '<a href="' . esc_url( 'https://github.com/awethemes/awebooking' ) . '" aria-label="' . esc_attr__( 'Contribute', 'awebooking' ) . '">' . esc_html__( 'Contribute', 'awebooking' ) . '</a>',
		);

		return array_merge( (array) $links, $row_meta );
	}

	/**
	 * Display error messages of add-ons.
	 *
	 * @access private
	 *
	 * @param  string $plugin_file Path to the plugin file, relative to the plugins directory.
	 * @param  array  $plugin_data An array of plugin data.
	 * @param  string $status      Status of the plugin.
	 */
	public function plugin_addon_notices( $plugin_file, $plugin_data, $status ) {
		static $failed_addons;

		// Cache this list addons for use less memory.
		if ( is_null( $failed_addons ) ) {
			$failed_addons = Collection::make( $this->awebooking->get_failed_addons() )
				->reject(function( $addon ) {
					return ! $addon->is_wp_plugin();
				})
				->keyBy(function( $addon ) {
					return $addon->get_basename();
				});
		}

		// Ignore outside scope of AweBooking addons.
		if ( ! $failed_addons->has( $plugin_file ) ) {
			return;
		}

		$addon = $failed_addons->get( $plugin_file );
		if ( ! $addon->has_errors() ) {
			return;
		}

		printf(
			'<tr class="awebooking-addon-notice-tr plugin-update-tr active"><td colspan="3" class="awebooking-addon-notice plugin-update colspanchange"><div class="notice inline notice-warning notice-alt"><strong>%1$s</strong><ul>%2$s</ul></div></td></tr>',
			esc_html__( 'This plugin has been activated but cannot be loaded by AweBooking by reason(s):', 'awebooking' ),
			'<li>' . wp_kses_post( implode( '</li><li>', $addon->get_errors() ) ) . '</li>'
		);
	}

	/**
	 * Update version to current.
	 */
	public function update_version() {
		delete_option( 'awebooking_version' );

		add_option( 'awebooking_version', $this->awebooking->version() );
	}

	/**
	 * Update DB version to current or special version.
	 *
	 * @param  string $version Optional, special version to set.
	 * @return boolean
	 */
	public function update_db_version( $version = null ) {
		delete_option( 'awebooking_db_version' );

		return add_option( 'awebooking_db_version', is_null( $version ) ? $this->awebooking->version() : $version );
	}

	/**
	 * Get current version store in wp-options.
	 *
	 * @return string|null
	 */
	public function get_current_version() {
		$version = get_option( 'awebooking_version', null );

		return $version ?: null;
	}

	/**
	 * Get current DB version store in wp-options.
	 *
	 * @return string|null
	 */
	public function get_current_db_version() {
		$version = get_option( 'awebooking_db_version', null );

		return $version ?: null;
	}

	/**
	 * Is this a brand new AweBooking install?
	 *
	 * @return boolean
	 */
	public function is_new_install() {
		return is_null( $this->get_current_version() ) && is_null( $this->get_current_db_version() );
	}

	/**
	 * Is database update needed?
	 *
	 * @return boolean
	 */
	public function needs_db_update() {
		$current_db_version = $this->get_current_db_version();

		// If no db_version found, there's nothing to update.
		if ( is_null( $current_db_version ) ) {
			return false;
		}

		return version_compare( $current_db_version, max( array_keys( $this->db_updates ) ), '<' );
	}

	/**
	 * Setup AweBooking environment - post-types, taxonomies, endpoints.
	 */
	protected function setup_environment() {
		if ( ! class_exists( 'Skeleton\Post_Type' ) ) {
			skeleton_psr4_autoloader( 'Skeleton\\', dirname( __DIR__ ) . '/vendor/awethemes/skeleton/inc/' );
		}

		$environment = new Setup_Environment;

		$environment->register_taxonomies();
		$environment->register_post_types();
		$environment->register_endpoints();
	}

	/**
	 * Create cron jobs.
	 */
	protected function create_cron_jobs() {
		// TODO: ...
	}

	/**
	 * Default options.
	 *
	 * Sets up the default options used on the settings page.
	 */
	protected function create_options() {
		// TODO: ...
	}

	/**
	 * Set up the database tables.
	 *
	 * @see https://codex.wordpress.org/Creating_Tables_with_Plugins
	 */
	protected function create_tables() {
		global $wpdb;

		$wpdb->hide_errors();

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		dbDelta( $this->get_db_schema() );
	}

	/**
	 * Get tables schema to create.
	 *
	 * @return string
	 */
	protected function get_db_schema() {
		global $wpdb;

		$collate = '';
		if ( $wpdb->has_cap( 'collation' ) ) {
			$collate = $wpdb->get_charset_collate();
		}

		$days_schema = '';
		for ( $i = 1; $i <= 31; $i++ ) {
			$days_schema .= '`d' . $i . '` BIGINT UNSIGNED NOT NULL DEFAULT 0,' . "\n";
		}

		$days_schema = trim( $days_schema );

		$tables = "
CREATE TABLE `{$wpdb->prefix}awebooking_rooms` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(191) DEFAULT NULL,
  `room_type` BIGINT UNSIGNED NOT NULL,
  `order` SMALLINT UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `name` (`name`),
  KEY `room_type` (`room_type`)
) $collate;
CREATE TABLE `{$wpdb->prefix}awebooking_booking` (
  `room_id` BIGINT UNSIGNED NOT NULL DEFAULT 0,
  `year` SMALLINT UNSIGNED NOT NULL DEFAULT 0,
  `month` TINYINT UNSIGNED NOT NULL DEFAULT 0,
  {$days_schema}
  PRIMARY KEY (`room_id`, `year`, `month`)
) $collate;
CREATE TABLE `{$wpdb->prefix}awebooking_availability` (
  `room_id` BIGINT UNSIGNED NOT NULL DEFAULT 0,
  `year` SMALLINT UNSIGNED NOT NULL DEFAULT 0,
  `month` TINYINT UNSIGNED NOT NULL DEFAULT 0,
  {$days_schema}
  PRIMARY KEY (`room_id`, `year`, `month`)
) $collate;
CREATE TABLE `{$wpdb->prefix}awebooking_pricing` (
  `rate_id` BIGINT UNSIGNED NOT NULL DEFAULT 0,
  `year` SMALLINT UNSIGNED NOT NULL DEFAULT 0,
  `month` TINYINT UNSIGNED NOT NULL DEFAULT 0,
  {$days_schema}
  PRIMARY KEY (`rate_id`, `year`, `month`)
) $collate;
CREATE TABLE {$wpdb->prefix}awebooking_booking_items (
  booking_item_id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  booking_item_name TEXT NOT NULL,
  booking_item_type varchar(191) NOT NULL DEFAULT '',
  booking_item_parent BIGINT UNSIGNED NOT NULL,
  booking_id BIGINT UNSIGNED NOT NULL,
  PRIMARY KEY (booking_item_id),
  KEY booking_id (booking_id),
  KEY booking_item_parent (booking_item_parent)
) $collate;
CREATE TABLE {$wpdb->prefix}awebooking_booking_itemmeta (
  meta_id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  booking_item_id BIGINT UNSIGNED NOT NULL,
  meta_key varchar(191) default NULL,
  meta_value longtext NULL,
  PRIMARY KEY (meta_id),
  KEY booking_item_id (booking_item_id),
  KEY meta_key (meta_key(32))
) $collate;
		";

		return $tables;
	}

	/**
	 * Create roles and capabilities.
	 *
	 * @access private
	 */
	public function create_roles() {
		global $wp_roles;

		if ( ! class_exists( 'WP_Roles' ) || is_null( $wp_roles ) ) {
			return;
		}

		// Hotel customer role: somebody who can only manage their profile, view and request some actions their booking.
		add_role( 'awebooking_customer', esc_html__( 'Hotel Customer', 'awebooking' ), [
			'read' => true,
		] );

		// Hotel receptionist: somebody who can view room types, services, amenities, manage to price, manage availability, assists guests making hotel reservations.
		add_role( 'awebooking_receptionist', esc_html__( 'Hotel Receptionist', 'awebooking' ), [
			'level_9'                => true,
			'level_8'                => true,
			'level_7'                => true,
			'level_6'                => true,
			'level_5'                => true,
			'level_4'                => true,
			'level_3'                => true,
			'level_2'                => true,
			'level_1'                => true,
			'level_0'                => true,
			'read'                   => true,
			'read_private_pages'     => true,
			'read_private_posts'     => true,
			'edit_posts'             => true,
			'edit_pages'             => true,
			'publish_posts'          => true,
			'publish_pages'          => true,
			'manage_links'           => true,
			'moderate_comments'      => true,
		] );

		foreach ( $this->get_core_receptionist_capabilities() as $cap_group ) {
			foreach ( $cap_group as $cap ) {
				$wp_roles->add_cap( 'awebooking_receptionist', $cap );
			}
		}

		// Hotel manager role: somebody who has access to all the AweBooking's features.
		add_role( 'awebooking_manager', esc_html__( 'Hotel Manager', 'awebooking' ), [
			'level_9'                => true,
			'level_8'                => true,
			'level_7'                => true,
			'level_6'                => true,
			'level_5'                => true,
			'level_4'                => true,
			'level_3'                => true,
			'level_2'                => true,
			'level_1'                => true,
			'level_0'                => true,
			'read'                   => true,
			'read_private_pages'     => true,
			'read_private_posts'     => true,
			'edit_users'             => true,
			'edit_posts'             => true,
			'edit_pages'             => true,
			'edit_published_posts'   => true,
			'edit_published_pages'   => true,
			'edit_private_pages'     => true,
			'edit_private_posts'     => true,
			'edit_others_posts'      => true,
			'edit_others_pages'      => true,
			'publish_posts'          => true,
			'publish_pages'          => true,
			'delete_posts'           => true,
			'delete_pages'           => true,
			'delete_private_pages'   => true,
			'delete_private_posts'   => true,
			'delete_published_pages' => true,
			'delete_published_posts' => true,
			'delete_others_posts'    => true,
			'delete_others_pages'    => true,
			'manage_categories'      => true,
			'manage_links'           => true,
			'moderate_comments'      => true,
			'upload_files'           => true,
			'export'                 => true,
			'import'                 => true,
			'list_users'             => true,
		] );

		foreach ( $this->get_core_manager_capabilities() as $cap_group ) {
			foreach ( $cap_group as $cap ) {
				$wp_roles->add_cap( 'awebooking_manager', $cap );
				$wp_roles->add_cap( 'administrator', $cap );
			}
		}
	}

	/**
	 * Remove roles
	 *
	 * @access private
	 */
	public function remove_roles() {
		global $wp_roles;

		if ( ! class_exists( 'WP_Roles' ) || is_null( $wp_roles ) ) {
			return;
		}

		foreach ( $this->get_core_receptionist_capabilities() as $cap_group ) {
			foreach ( $cap_group as $cap ) {
				$wp_roles->remove_cap( 'awebooking_receptionist', $cap );
			}
		}

		foreach ( $this->get_core_manager_capabilities() as $cap_group ) {
			foreach ( $cap_group as $cap ) {
				$wp_roles->remove_cap( 'awebooking_manager', $cap );
				$wp_roles->remove_cap( 'administrator', $cap );
			}
		}

		remove_role( 'awebooking_customer' );
		remove_role( 'awebooking_receptionist' );
		remove_role( 'awebooking_manager' );
	}

	/**
	 * Get manager capabilities for awebooking.
	 *
	 * @return array
	 */
	protected function get_core_manager_capabilities() {
		$capabilities = [];

		$capabilities['core'] = [
			'manage_awebooking',
			'manage_awebooking_settings',
		];

		$capability_types = [
			Constants::BOOKING,
			Constants::ROOM_TYPE,
			Constants::PRICING_RATE,
		];

		foreach ( $capability_types as $capability_type ) {

			$capabilities[ $capability_type ] = [
				// Post type.
				"edit_{$capability_type}",
				"read_{$capability_type}",
				"delete_{$capability_type}",
				"edit_{$capability_type}s",
				"edit_others_{$capability_type}s",
				"publish_{$capability_type}s",
				"read_private_{$capability_type}s",
				"delete_{$capability_type}s",
				"delete_private_{$capability_type}s",
				"delete_published_{$capability_type}s",
				"delete_others_{$capability_type}s",
				"edit_private_{$capability_type}s",
				"edit_published_{$capability_type}s",

				// Terms.
				"manage_{$capability_type}_terms",
				"edit_{$capability_type}_terms",
				"delete_{$capability_type}_terms",
				"assign_{$capability_type}_terms",
			];
		}

		return $capabilities;
	}

	/**
	 * Get receptionist capabilities for awebooking.
	 *
	 * @return array
	 */
	protected function get_core_receptionist_capabilities() {
		$capabilities = [];

		$capabilities['core'] = [
			'manage_awebooking',
		];

		// Room type.
		$room_type = AweBooking::ROOM_TYPE;

		$capabilities[ $room_type ] = [
			"read_{$room_type}",
			"edit_{$room_type}s",
			"edit_others_{$room_type}s",
			"read_private_{$room_type}s",

			"manage_{$room_type}_terms",
			// "assign_{$room_type}_terms",
		];

		// Pricing rate.
		$pricing_rate = AweBooking::PRICING_RATE;

		$capabilities[ $pricing_rate ] = [
			"read_{$pricing_rate}",
			"edit_{$pricing_rate}s",
			"edit_others_{$pricing_rate}s",
			"read_private_{$pricing_rate}s",
		];

		// Booking.
		$booking = AweBooking::BOOKING;

		$capabilities[ $booking ] = [
			// Post type.
			"edit_{$booking}",
			"read_{$booking}",
			"delete_{$booking}",
			"edit_{$booking}s",
			"edit_others_{$booking}s",
			"publish_{$booking}s",
			"read_private_{$booking}s",
			"delete_{$booking}s",
			"delete_private_{$booking}s",
			"delete_published_{$booking}s",
			"delete_others_{$booking}s",
			"edit_private_{$booking}s",
			"edit_published_{$booking}s",

			// Terms.
			"manage_{$booking}_terms",
			"edit_{$booking}_terms",
			"delete_{$booking}_terms",
			"assign_{$booking}_terms",
		];

		return $capabilities;
	}
}
