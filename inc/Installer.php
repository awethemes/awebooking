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
}
