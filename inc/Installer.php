<?php
namespace AweBooking;

use AweBooking\Booking\Booking;
use AweBooking\Booking\Items\Line_Item;
use AweBooking\Booking\Items\Service_Item;

class Installer {
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

		if ( version_compare( $db_version, '3.0.0-beta4', '<' ) ) {
			global $wpdb;

			$wpdb->hide_errors();

			require_once ABSPATH . 'wp-admin/includes/upgrade.php';
			dbDelta( "
				CREATE TABLE IF NOT EXISTS {$wpdb->prefix}awebooking_booking_items (
				  booking_item_id BIGINT UNSIGNED NOT NULL auto_increment,
				  booking_item_name TEXT NOT NULL,
				  booking_item_type varchar(200) NOT NULL DEFAULT '',
				  booking_item_parent BIGINT UNSIGNED NOT NULL,
				  booking_id BIGINT UNSIGNED NOT NULL,
				  PRIMARY KEY  (booking_item_id),
				  KEY booking_id (booking_id),
				  KEY booking_item_parent (booking_item_parent)
				);

				CREATE TABLE IF NOT EXISTS {$wpdb->prefix}awebooking_booking_itemmeta (
				  meta_id BIGINT UNSIGNED NOT NULL auto_increment,
				  booking_item_id BIGINT UNSIGNED NOT NULL,
				  meta_key varchar(255) default NULL,
				  meta_value longtext NULL,
				  PRIMARY KEY  (meta_id),
				  KEY booking_item_id (booking_item_id),
				  KEY meta_key (meta_key(32))
				);
			" );

			// --------------------------
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
				$booking_item['name']      = $booking->get_meta( 'booking_room_type_title' );
				$booking_item['room_id']   = $booking->get_meta( 'booking_room_id' );
				$booking_item['check_in']  = $booking->get_meta( 'booking_check_in' );
				$booking_item['check_out'] = $booking->get_meta( 'booking_check_out' );
				$booking_item['adults']    = $booking->get_meta( 'booking_adults' );
				$booking_item['children']  = $booking->get_meta( 'booking_children' );
				$booking_item['total']     = floatval( $booking->get_meta( 'room_total' ) ) + floatval( $booking->get_meta( 'services_total' ) );

				$booking->add_item( $booking_item );

				try {
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
					update_metadata( 'awebooking', $booking->ID, $to_metaname,
						$booking->get_meta( $from_metaname )
					);
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

		// utf8mb4 conversion.
		// maybe_convert_table_to_utf8mb4( $wpdb->dmtable );
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
			$command .= '`d' . $i . '` BIGINT UNSIGNED NOT NULL DEFAULT 0, ';
		}

		return $command;
	}

	/**
	 * Create roles and capabilities.
	 */
	public static function create_roles() {
		global $wp_roles;

		if ( ! class_exists( 'WP_Roles' ) ) {
			return;
		}

		if ( ! isset( $wp_roles ) ) {
			$wp_roles = new WP_Roles();
		}

		// Customer role
		add_role( 'awebooking_customer', __( 'Hotel customer', 'awebooking' ), [
			'read' 					 => true,
		] );

		add_role( 'awebooking_receptionist', __( 'Hotel receptionist', 'awebooking' ), [
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
			// 'edit_published_posts'   => true,
			// 'edit_published_pages'   => true,
			// 'edit_private_pages'     => true,
			// 'edit_private_posts'     => true,
			// 'edit_others_posts'      => true,
			// 'edit_others_pages'      => true,
			'publish_posts'          => true,
			'publish_pages'          => true,
			// 'delete_posts'           => true,
			// 'delete_pages'           => true,
			// 'delete_private_pages'   => true,
			// 'delete_private_posts'   => true,
			// 'delete_published_pages' => true,
			// 'delete_published_posts' => true,
			// 'delete_others_posts'    => true,
			// 'delete_others_pages'    => true,
			'manage_categories'      => true,
			'manage_links'           => true,
			'moderate_comments'      => true,
		] );

		$receptionist_capabilities = self::get_core_receptionist_capabilities();

		foreach ( $receptionist_capabilities as $cap_group ) {
			foreach ( $cap_group as $cap ) {
				$wp_roles->add_cap( 'awebooking_receptionist', $cap );
			}
		}

		// Shop manager role
		add_role( 'awebooking_manager', __( 'Hotel manager', 'awebooking' ), [
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

		$manager_capabilities = self::get_core_manager_capabilities();

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

		if ( ! class_exists( 'WP_Roles' ) ) {
			return;
		}

		if ( ! isset( $wp_roles ) ) {
			$wp_roles = new WP_Roles();
		}

		$receptionist_capabilities = self::get_core_receptionist_capabilities();

		foreach ( $receptionist_capabilities as $cap_group ) {
			foreach ( $cap_group as $cap ) {
				$wp_roles->remove_cap( 'awebooking_receptionist', $cap );
			}
		}

		$manager_capabilities = self::get_core_manager_capabilities();

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
		$capabilities = array();

		$capabilities['core'] = [
			'manage_awebooking',
			'manage_awebooking_settings',
		];

		$capability_types = [ AweBooking::ROOM_TYPE, AweBooking::BOOKING, AweBooking::PRICING_RATE ];

		foreach ( $capability_types as $capability_type ) {

			$capabilities[ $capability_type ] = [
				// Post type
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

				// Terms
				"manage_{$capability_type}_terms",
				"edit_{$capability_type}_terms",
				"delete_{$capability_type}_terms",
				"assign_{$capability_type}_terms",
			];
		}

		return $capabilities;
	}

	/**
	 * Get manager capabilities for awebooking.
	 *
	 * @return array
	 */
	 private static function get_core_receptionist_capabilities() {
		$capabilities = array();

		$capabilities['core'] = [
			'manage_awebooking',
		];

		$room_type = AweBooking::ROOM_TYPE;


		$capabilities[ $room_type ] = [
			"read_{$room_type}",
			"edit_{$room_type}s",
			"edit_others_{$room_type}s",
			"read_private_{$room_type}s",
		];

		$pricing_rate = AweBooking::PRICING_RATE;


		$capabilities[ $pricing_rate ] = [
			"read_{$pricing_rate}",
			"edit_{$pricing_rate}s",
			"edit_others_{$pricing_rate}s",
			"read_private_{$pricing_rate}s",
		];

		$booking = AweBooking::BOOKING;


		$capabilities[ $booking ] = [
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

			// Terms
			"manage_{$booking}_terms",
			"edit_{$booking}_terms",
			"delete_{$booking}_terms",
			"assign_{$booking}_terms",
		];

		return $capabilities;
	}
}
