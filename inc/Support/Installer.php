<?php
namespace AweBooking\Support;

class Installer {

	/**
	 * Set up the database tables which the plugin needs to function.
	 *
	 * @see https://codex.wordpress.org/Creating_Tables_with_Plugins
	 */
	public static function create_tables() {
		global $wpdb;

		$wpdb->hide_errors();
		// $wpdb->show_errors();

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		dbDelta( static::get_schema() );
	}

	/**
	 * Get Table schema.
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
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `room_type` bigint UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  KEY `name` (`name`),
  KEY `room_type` (`room_type`)
) $collate;

CREATE TABLE `{$wpdb->prefix}awebooking_booking` (
  `room_id` bigint UNSIGNED NOT NULL DEFAULT 0,
  `year` integer NOT NULL DEFAULT 0,
  `month` integer NOT NULL DEFAULT 0,
  " . static::generate_days_schema() . "
  PRIMARY KEY (`room_id`, `year`, `month`)
) $collate;

CREATE TABLE `{$wpdb->prefix}awebooking_availability` (
  `room_id` bigint UNSIGNED NOT NULL DEFAULT 0,
  `year` integer NOT NULL DEFAULT 0,
  `month` integer NOT NULL DEFAULT 0,
  " . static::generate_days_schema() . "
  PRIMARY KEY (`room_id`, `year`, `month`)
) $collate;

CREATE TABLE `{$wpdb->prefix}awebooking_pricing` (
  `rate_id` bigint UNSIGNED NOT NULL DEFAULT 0,
  `year` integer NOT NULL DEFAULT 0,
  `month` integer NOT NULL DEFAULT 0,
  " . static::generate_days_schema() . "
  PRIMARY KEY (`rate_id`, `year`, `month`)
) $collate;

CREATE TABLE {$wpdb->prefix}awebooking_booking_items (
  booking_item_id BIGINT UNSIGNED NOT NULL auto_increment,
  booking_item_name TEXT NOT NULL,
  booking_item_type varchar(200) NOT NULL DEFAULT '',
  booking_item_parent BIGINT UNSIGNED NOT NULL,
  booking_id BIGINT UNSIGNED NOT NULL,
  PRIMARY KEY  (booking_item_id),
  KEY booking_id (booking_id),
  KEY booking_item_parent (booking_item_parent)
) $collate;

CREATE TABLE {$wpdb->prefix}awebooking_booking_itemmeta (
  meta_id BIGINT UNSIGNED NOT NULL auto_increment,
  booking_item_id BIGINT UNSIGNED NOT NULL,
  meta_key varchar(255) default NULL,
  meta_value longtext NULL,
  PRIMARY KEY  (meta_id),
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
			$command .= '`d' . $i . '` integer NOT NULL DEFAULT 0, ';
		}

		return $command;
	}
}
