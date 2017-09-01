<?php
namespace AweBooking;

use AweBooking\Booking\Booking;
use AweBooking\Booking\Items\Line_Item;
use AweBooking\Booking\Items\Service_Item;

class Installer {
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

		if ( version_compare( $db_version, '3.0.0-beta4', '<' ) ) {
			global $wpdb;

			$services = $wpdb->get_results( "
				SELECT term.* FROM `{$wpdb->terms}` AS term
				LEFT JOIN `{$wpdb->term_taxonomy}` AS tt ON `term`.`term_id` = `tt`.`term_id`
				WHERE tt.taxonomy = 'hotel_extra_service'
			" );

			foreach ( $services as $service ) {
				update_term_meta( $service->term_id, '_service_operation', get_term_meta( $service->term_id, 'operation', true ) );
				delete_term_meta( $service->term_id, 'operation' );

				update_term_meta( $service->term_id, '_service_value', get_term_meta( $service->term_id, 'price', true ) );
				delete_term_meta( $service->term_id, 'price' );

				update_term_meta( $service->term_id, '_service_type', get_term_meta( $service->term_id, 'type', true ) );
				delete_term_meta( $service->term_id, 'type' );
			}

			// --------------------------
			$transform_booking_metadata = [
				'customer_id'         => '_customer_id',
				'customer_note'       => '_customer_note',
				'customer_first_name' => '_customer_first_name',
				'customer_last_name'  => '_customer_last_name',
				'customer_email'      => '_customer_email',
				'customer_phone'      => '_customer_phone',
				'customer_company'    => '_customer_company',
				'customer_phone'      => '_customer_phone',
				'currency'            => '_currency',
				'total_price'         => '_total',
			];

			$old_metadata = [
				'booking_adults',
				'booking_children',
				'booking_check_in',
				'booking_check_out',
				'room_total',
				'services_total',
				'booking_request_services',
				'booking_room_id',
				'booking_room_name',
				'booking_room_type_id',
				'booking_room_type_title',
				'booking_hotel_location',
			];

			$bookings = $wpdb->get_results( "SELECT ID FROM {$wpdb->posts} WHERE post_type = 'awebooking' AND post_status != 'auto-draft';" );

			foreach ( $bookings as $booking ) {
				$booking = new Booking( $booking->ID );

				$booking_item = new Line_Item;
				$booking_item['name']      = get_post_meta( $booking->ID, 'booking_room_type_title', true );
				$booking_item['room_id']   = get_post_meta( $booking->ID, 'booking_room_id', true );
				$booking_item['check_in']  = get_post_meta( $booking->ID, 'booking_check_in', true );
				$booking_item['check_out'] = get_post_meta( $booking->ID, 'booking_check_out', true );
				$booking_item['adults']    = get_post_meta( $booking->ID, 'booking_adults', true );
				$booking_item['children']  = get_post_meta( $booking->ID, 'booking_children', true );
				$booking_item['total']     = floatval( get_post_meta( $booking->ID, 'room_total', true ) ) + floatval( get_post_meta( $booking->ID, 'services_total', true ) );

				try {
					$booking->add_item( $booking_item );
					$booking->save();
				} catch ( \Exception $e ) {
					continue;
				}

				if ( ! $booking_item->exists() ) {
					continue;
				}

				$booking_services = get_post_meta( $booking->ID, 'booking_request_services', true );
				if ( ! empty( $booking_services ) && is_array( $booking_services ) ) {
					foreach ( $booking_services as $service_id => $quantity ) {
						$service = new Service( $service_id );
						if ( ! $service->exists() ) {
							continue;
						}

						$service_item = new Service_Item( $booking->ID );
						$service_item['name']       = $service->get_name();
						$service_item['service_id'] = $service->get_id();
						$service_item['parent_id']  = $booking_item->get_id();

						$service_item->save();
					}
				}

				foreach ( $transform_booking_metadata as $from_metaname => $to_metaname ) {
					update_post_meta( $booking->ID, $to_metaname,
						get_post_meta( $booking->ID, $from_metaname, true )
					);

					delete_post_meta( $booking->ID, $from_metaname );
				}

				foreach ( $old_metadata as $delete_metadata ) {
					delete_post_meta( $booking->ID, $delete_metadata );
				}
			} // End foreach().
		} // End if().

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
