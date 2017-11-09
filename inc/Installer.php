<?php
namespace AweBooking;

use AweBooking\Booking\Booking;
use AweBooking\Booking\Items\Line_Item;
use AweBooking\Booking\Items\Service_Item;

class Installer {
	/**
	 * DB updates and callbacks that need to be run per version.
	 *
	 * @var array
	 */
	private static $db_updates = [
		'3.0.0-beta10' => array(
			'awebooking_update_300_beta10_fix_db_types',
		),
	];

	/**
	 * Check AweBooking version and run the updater is required.
	 *
	 * This check is done on all requests and runs if the versions do not match.
	 */
	public static function check_version() {
		if ( ! defined( 'IFRAME_REQUEST' ) && get_option( 'awebooking_version' ) !== AweBooking::VERSION ) {
			static::install();

			do_action( 'awebooking_updated' );
		}
	}

	/**
	 * Install the AweBooking.
	 *
	 * @return void
	 */
	public static function install() {
		$awebooking = awebooking();

		$core_hooks = new WP_Core_Hooks( $awebooking );
		$core_hooks->init( $awebooking );

		static::create_tables();
		static::create_default_location();
		static::remove_roles();
		static::create_roles();

		$current_version = get_option( 'awebooking_version', null );
		$current_db_version = get_option( 'awebooking_db_version', null );

		// No versions? This is a new install :).
		if ( is_null( $current_version ) && is_null( $current_db_version ) && apply_filters( 'awebooking/enable_setup_wizard', true ) ) {
			set_transient( '_awebooking_activation_redirect', 1, 30 );
		}

		delete_option( 'awebooking_version' );
		add_option( 'awebooking_version', AweBooking::VERSION );

		@flush_rewrite_rules();
	}

	public static function update() {
		$awebooking = awebooking();
		$db_version = get_option( 'awebooking_version' );

		$update_queued = false;
		$background_updater = awebooking()->make( Background_Updater::class );

		foreach ( static::$db_updates as $version => $update_callbacks ) {
			if ( version_compare( $db_version, $version, '<' ) ) {
				foreach ( $update_callbacks as $update_callback ) {
					$background_updater->push_to_queue( $update_callback );
					$update_queued = true;
				}
			}
		}

		if ( $update_queued ) {
			$background_updater->save()->dispatch();
		}

		delete_option( 'awebooking_version' );
		add_option( 'awebooking_version', AweBooking::VERSION );
	}

	/**
	 * Set up the database tables which the plugin needs to function.
	 *
	 * @see https://codex.wordpress.org/Creating_Tables_with_Plugins
	 */
	public static function create_tables() {
		global $wpdb;
		$wpdb->hide_errors();

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta( static::get_schema() );
	}

	private static function create_default_location() {
		// Hotel location.
		$cat_name = esc_html__( 'Hotel Location', 'awebooking' );

		/* translators: Default hotel location slug */
		$cat_slug = sanitize_title( esc_html_x( 'Hotel Location', 'Default hotel location slug', 'awebooking' ) );

		// TODO: ...
	}

	/**
	 * Get tables schema to create.
	 *
	 * @return string
	 */
	private static function get_schema() {
		global $wpdb;

		$collate = '';
		if ( $wpdb->has_cap( 'collation' ) ) {
			$collate = $wpdb->get_charset_collate();
		}

		$tables = "
CREATE TABLE `{$wpdb->prefix}awebooking_rooms` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(191) DEFAULT NULL,
  `room_type` BIGINT UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  KEY `name` (`name`),
  KEY `room_type` (`room_type`)
) $collate;

CREATE TABLE `{$wpdb->prefix}awebooking_booking` (
  `room_id` BIGINT UNSIGNED NOT NULL DEFAULT 0,
  `year` SMALLINT UNSIGNED NOT NULL DEFAULT 0,
  `month` TINYINT UNSIGNED NOT NULL DEFAULT 0,
  " . static::generate_days_schema() . "
  PRIMARY KEY (`room_id`, `year`, `month`)
) $collate;

CREATE TABLE `{$wpdb->prefix}awebooking_availability` (
  `room_id` BIGINT UNSIGNED NOT NULL DEFAULT 0,
  `year` SMALLINT UNSIGNED NOT NULL DEFAULT 0,
  `month` TINYINT UNSIGNED NOT NULL DEFAULT 0,
  " . static::generate_days_schema() . "
  PRIMARY KEY (`room_id`, `year`, `month`)
) $collate;

CREATE TABLE `{$wpdb->prefix}awebooking_pricing` (
  `rate_id` BIGINT UNSIGNED NOT NULL DEFAULT 0,
  `year` SMALLINT UNSIGNED NOT NULL DEFAULT 0,
  `month` TINYINT UNSIGNED NOT NULL DEFAULT 0,
  " . static::generate_days_schema() . "
  PRIMARY KEY (`rate_id`, `year`, `month`)
) $collate;

CREATE TABLE {$wpdb->prefix}awebooking_booking_items (
  booking_item_id BIGINT UNSIGNED NOT NULL auto_increment,
  booking_item_name TEXT NOT NULL,
  booking_item_type varchar(191) NOT NULL DEFAULT '',
  booking_item_parent BIGINT UNSIGNED NOT NULL,
  booking_id BIGINT UNSIGNED NOT NULL,
  PRIMARY KEY (booking_item_id),
  KEY booking_id (booking_id),
  KEY booking_item_parent (booking_item_parent)
) $collate;

CREATE TABLE {$wpdb->prefix}awebooking_booking_itemmeta (
  meta_id BIGINT UNSIGNED NOT NULL auto_increment,
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
	 * Generate days schema.
	 *
	 * @return string
	 */
	private static function generate_days_schema() {
		$command = '';

		for ( $i = 1; $i <= 31; $i++ ) {
			$command .= '`d' . $i . '` BIGINT UNSIGNED NOT NULL DEFAULT 0,' . "\n";
		}

		return trim( $command );
	}

	/**
	 * Create roles and capabilities.
	 */
	public static function create_roles() {
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

		$receptionist_capabilities = static::get_core_receptionist_capabilities();

		foreach ( $receptionist_capabilities as $cap_group ) {
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

		$manager_capabilities = static::get_core_manager_capabilities();

		foreach ( $manager_capabilities as $cap_group ) {
			foreach ( $cap_group as $cap ) {
				$wp_roles->add_cap( 'awebooking_manager', $cap );
				$wp_roles->add_cap( 'administrator', $cap );
			}
		}
	}

	/**
	 * Remove roles
	 */
	public static function remove_roles() {
		global $wp_roles;

		if ( ! class_exists( 'WP_Roles' ) || is_null( $wp_roles ) ) {
			return;
		}

		$receptionist_capabilities = static::get_core_receptionist_capabilities();

		foreach ( $receptionist_capabilities as $cap_group ) {
			foreach ( $cap_group as $cap ) {
				$wp_roles->remove_cap( 'awebooking_receptionist', $cap );
			}
		}

		$manager_capabilities = static::get_core_manager_capabilities();

		foreach ( $manager_capabilities as $cap_group ) {
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
	 private static function get_core_manager_capabilities() {
		$capabilities = [];

		$capabilities['core'] = [
			'manage_awebooking',
			'manage_awebooking_settings',
		];

		$capability_types = [ AweBooking::ROOM_TYPE, AweBooking::BOOKING, AweBooking::PRICING_RATE ];

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
	 private static function get_core_receptionist_capabilities() {
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
