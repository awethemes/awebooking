<?php

namespace AweBooking;

use Psr\Log\LoggerInterface;
use AweBooking\Admin\Admin_Settings;
use AweBooking\Core\Bootstrap\Setup_Environment;
use AweBooking\Background_Process\Background_Updater;

class Installer {
	/**
	 * The plugin class instance.
	 *
	 * @var \AweBooking\Plugin
	 */
	protected $plugin;

	/**
	 * The background updater.
	 *
	 * @var \AweBooking\Background_Process\Background_Updater
	 */
	protected $background_updater;

	/**
	 * DB updates and callbacks that need to be run per version.
	 *
	 * @var array
	 */
	protected $db_updates = [
		'3.1.0' => [
			'abrs_update_310_bookings',
			'abrs_update_310_room_types',
			'abrs_update_310_migrate_services',
			'abrs_update_310_db_version',
		],
		'3.1.10' => [
			'abrs_update_3110_remove_table_relationship',
			'abrs_update_3110_db_version',
		],
	];

	/**
	 * Constructor.
	 *
	 * @param \AweBooking\Plugin $plugin The plugin class instance.
	 */
	public function __construct( Plugin $plugin ) {
		$this->plugin = $plugin;
	}

	/**
	 * Returns the plugin instance.
	 *
	 * @return \AweBooking\Plugin
	 */
	public function get_plugin() {
		return $this->plugin;
	}

	/**
	 * Hooks in the WordPress.
	 *
	 * @return void
	 */
	public function init() {
		add_action( 'init', [ $this, 'maybe_reinstall' ], 5 );
		add_action( 'init', [ $this, 'init_background_updater' ], 5 );
		add_action( 'init', [ $this, 'register_metadata_table' ], 0 );
		add_action( 'admin_init', [ $this, 'maybe_create_options' ], 10 );
		add_filter( 'wpmu_drop_tables', [ $this, 'wpmu_drop_tables' ] );
		add_filter( 'plugin_row_meta', [ $this, 'plugin_row_meta' ], 10, 2 );
		add_filter( "plugin_action_links_{$this->plugin->plugin_basename()}", [ $this, 'plugin_action_links' ] );

		if ( $this->plugin->bound( 'relationships' ) ) {
			$this->plugin['relationships']->init();
		}
	}

	/**
	 * Doing action on the activation the awebooking.
	 *
	 * @return void
	 */
	public function activation() {
		if ( apply_filters( 'abrs_enable_setup_wizard', $this->is_new_install() ) ) {
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
		delete_transient( 'awebooking_installing' );
	}

	/**
	 * Init background updates.
	 *
	 * @return void
	 */
	public function init_background_updater() {
		$this->background_updater = new Background_Updater( $this, $this->plugin->get_logger() );
	}

	/**
	 * Check AweBooking version and run the reinstall is required.
	 *
	 * This check is done on all requests and runs if the versions do not match.
	 *
	 * @access private
	 */
	public function maybe_reinstall() {
		if ( ! defined( 'IFRAME_REQUEST' ) && version_compare( $this->get_current_version(), $this->plugin->version(), '<' ) ) {
			Constants::define( 'AWEBOOKING_REINSTALLING', true );

			$this->install();

			do_action( 'awebookin_updated' );
		}
	}

	/**
	 * Check to create the default options.
	 *
	 * @return void
	 */
	public function maybe_create_options() {
		if ( ! is_admin() || ! did_action( 'admin_init' ) ) {
			return;
		}

		if ( ! get_option( Constants::OPTION_KEY ) ) {
			$admin_settings = $this->plugin->make( Admin_Settings::class );

			add_option( Constants::OPTION_KEY, $admin_settings->get_default_settings(), '', 'yes' );
		}
	}

	/**
	 * Support awebooking tables and item-metadata.
	 *
	 * @return void
	 */
	public function register_metadata_table() {
		global $wpdb;

		$wpdb->tables[] = 'awebooking_booking_itemmeta';
		$wpdb->booking_itemmeta = $wpdb->prefix . 'awebooking_booking_itemmeta';

		$wpdb->tables[] = 'awebooking_relationshipmeta';
		$wpdb->awebooking_relationshipmeta = $wpdb->prefix . 'awebooking_relationshipmeta';
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
		$tables[] = $wpdb->prefix . 'awebooking_tax_rates';

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
		return array_merge([
			'settings' => '<a href="' . esc_url( admin_url( 'admin.php?awebooking=/settings' ) ) . '" aria-label="' . esc_attr__( 'View settings', 'awebooking' ) . '">' . esc_html__( 'Settings', 'awebooking' ) . '</a>',
		], $links );
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
		if ( $this->plugin->plugin_basename() !== $file ) {
			return (array) $links;
		}

		return array_merge( (array) $links, [
			'docs'       => '<a href="' . esc_url( 'http://docs.awethemes.com/awebooking' ) . '" aria-label="' . esc_attr__( 'View documentation', 'awebooking' ) . '">' . esc_html__( 'Docs', 'awebooking' ) . '</a>',
			'demo'       => '<a href="' . esc_url( 'http://demo.awethemes.com/awebooking' ) . '" aria-label="' . esc_attr__( 'Visit demo', 'awebooking' ) . '">' . esc_html__( 'Demo', 'awebooking' ) . '</a>',
			'contribute' => '<a href="' . esc_url( 'https://github.com/awethemes/awebooking' ) . '" aria-label="' . esc_attr__( 'Contribute', 'awebooking' ) . '">' . esc_html__( 'Contribute', 'awebooking' ) . '</a>',
		]);
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

		// Require core functions.
		require_once trailingslashit( __DIR__ ) . 'Core/functions.php';
		require_once trailingslashit( __DIR__ ) . 'Admin/admin-functions.php';

		$this->setup_environment();
		$this->create_tables();
		$this->create_roles();
		$this->create_cron_jobs();
		// $this->create_default_hotel();
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
		do_action( 'awebooking_installed' );

		// Remove rewrite rules and then recreate rewrite rules.
		@flush_rewrite_rules();
	}

	/**
	 * Push all needed DB updates to the queue for processing.
	 *
	 * @return void
	 */
	protected function update() {
		$logger = $this->plugin->make( LoggerInterface::class );

		if ( null === $this->background_updater ) {
			$this->init_background_updater();
		}

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
				$logger->info( sprintf( '[%1$s] Queuing %2$s', $version, $update_callback ), [ 'source' => 'db_updates' ] );

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
	 * Update version to current.
	 *
	 * @return void
	 */
	public function update_version() {
		delete_option( 'awebooking_version' );

		add_option( 'awebooking_version', $this->plugin->version() );
	}

	/**
	 * Update DB version to current or specified version.
	 *
	 * @param  string $version Optional, specified version to set.
	 * @return boolean
	 */
	public function update_db_version( $version = null ) {
		delete_option( 'awebooking_db_version' );

		return add_option( 'awebooking_db_version', is_null( $version ) ? $this->plugin->version() : $version );
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
		if ( empty( $this->db_updates ) || is_null( $current_db_version ) ) {
			return false;
		}

		return version_compare( $current_db_version, max( array_keys( $this->db_updates ) ), '<' );
	}

	/**
	 * Setup environment - post-types, taxonomies, endpoints.
	 *
	 * @return void
	 */
	protected function setup_environment() {
		$environment = new Setup_Environment( $this->plugin );

		$environment->register_taxonomies();
		$environment->register_post_types();
		$environment->register_post_status();

		$environment->register_endpoints();
	}

	/**
	 * Create cron jobs.
	 *
	 * @return void
	 */
	protected function create_cron_jobs() {
	}

	/**
	 * Check to create the default hotel.
	 *
	 * @return void
	 */
	protected function create_default_hotel() {
		global $wpdb;

		if ( abrs_get_page_id( 'primary_hotel' ) > 0 ) {
			return;
		}

		// Default category name.
		$hotel_name = esc_html__( 'Default Hotel', 'awebooking' );

		/* translators: Default hotel slug */
		$hotel_slug = sanitize_title( _x( 'Default Hotel', 'Default hotel slug', 'awebooking' ) );

		// Find the default hotel in the database.
		$hotel_id = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM {$wpdb->posts} WHERE post_name = %s AND post_type = %s", $hotel_name, Constants::HOTEL_LOCATION ) );

		if ( is_null( $hotel_id ) ) {
			$hotel_id = wp_insert_post([
				'post_title'  => $hotel_name,
				'post_name'   => $hotel_slug,
				'post_type'   => Constants::HOTEL_LOCATION,
				'post_status' => 'publish',
			] );
		}

		abrs_update_option( [ 'page_primary_hotel' => $hotel_id ] );
	}

	/**
	 * Create roles and capabilities.
	 *
	 * @access private
	 */
	protected function create_roles() {
		( new Roles )->create();
	}

	/**
	 * Set up the database tables.
	 *
	 * @see https://codex.wordpress.org/Creating_Tables_with_Plugins
	 *
	 * @return void
	 */
	protected function create_tables() {
		global $wpdb;

		$wpdb->show_errors();

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		dbDelta( $this->get_db_schema() );

		if ( $this->plugin->bound( 'relationships' ) ) {
			$this->plugin['relationships']->get_storage()->install();
		}
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
  `name` VARCHAR(191) DEFAULT NULL,
  `room_type` BIGINT UNSIGNED NOT NULL,
  `order` BIGINT UNSIGNED NOT NULL DEFAULT 0,
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
CREATE TABLE `{$wpdb->prefix}awebooking_booking_items` (
  `booking_item_id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `booking_item_name` VARCHAR(255) NOT NULL,
  `booking_item_type` VARCHAR(191) NOT NULL DEFAULT '',
  `booking_item_parent` BIGINT UNSIGNED NOT NULL,
  `object_id` BIGINT UNSIGNED NOT NULL,
  `booking_id` BIGINT UNSIGNED NOT NULL,
  PRIMARY KEY (`booking_item_id`),
  KEY `booking_item_parent` (`booking_item_parent`),
  KEY `object_id` (`object_id`),
  KEY `booking_id` (`booking_id`)
) $collate;
CREATE TABLE `{$wpdb->prefix}awebooking_booking_itemmeta` (
  `meta_id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `booking_item_id` BIGINT UNSIGNED NOT NULL,
  `meta_key` VARCHAR(191) DEFAULT NULL,
  `meta_value` LONGTEXT NULL,
  PRIMARY KEY (`meta_id`),
  KEY `booking_item_id` (`booking_item_id`),
  KEY `meta_key` (meta_key(32))
) $collate;
CREATE TABLE `{$wpdb->prefix}awebooking_tax_rates` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `rate` VARCHAR(8) NOT NULL DEFAULT '',
  `name` VARCHAR(191) NOT NULL DEFAULT '',
  `priority` BIGINT UNSIGNED NOT NULL DEFAULT 0,
  `compound` INT(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `name` (`name`)
) $collate;
";

		return $tables;
	}
}
