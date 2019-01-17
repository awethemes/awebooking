<?php

namespace AweBooking\Admin;

use WP_Error;
use AweBooking\Roles;
use Illuminate\Support\Arr;

class Tools {
	/**
	 * A list of available tools for use in the system status section.
	 *
	 * @return array
	 */
	public static function all() {
		return apply_filters( 'abrs_debug_tools', [
			'clear_expired_transients' => [
				'name'     => esc_html__( 'Expired transients', 'awebooking' ),
				'button'   => esc_html__( 'Clear transients', 'awebooking' ),
				'desc'     => esc_html__( 'This tool will clear ALL expired transients from WordPress.', 'awebooking' ),
				'callback' => [ __CLASS__, 'clear_expired_transients' ],
			],
			'optimize_database'        => [
				'name'     => esc_html__( 'Optimize database', 'awebooking' ),
				'button'   => esc_html__( 'Optimize', 'awebooking' ),
				'desc'     => esc_html__( 'This tool will delete all orphan rows in AweBooking tables.', 'awebooking' ),
				'callback' => [ __CLASS__, 'optimize_database' ],
			],
			'reset_roles' => array(
				'name'     => esc_html__( 'Capabilities', 'awebooking' ),
				'button'   => esc_html__( 'Reset capabilities', 'awebooking' ),
				'desc'     => esc_html__( 'This tool will reset the admin, customer, receptionist and hotel manager roles to default.', 'awebooking' ),
				'callback' => [ __CLASS__, 'reset_roles' ],
			),
			/*'set_orphan_rooms' => array(
				'name'     => esc_html__( 'Update room types missing Hotel', 'awebooking' ),
				'button'   => esc_html__( 'Update', 'awebooking' ),
				'desc'     => esc_html__( '...', 'awebooking' ),
				'callback' => [ __CLASS__, 'set_orphan_rooms' ],
			),*/
		]);
	}

	/**
	 * Run a task.
	 *
	 * @param  string $task The task name.
	 * @return \stdClass|\WP_Error
	 */
	public static function run( $task ) {
		$tasks = static::all();

		if ( ! array_key_exists( $task, $tasks ) ) {
			return new WP_Error( 'invalid', esc_html__( 'Task is not registered', 'awebooking' ), $task );
		}

		$callback = Arr::get( $tasks, $task . '.callback' );

		if ( ! is_callable( $callback ) ) {
			return new WP_Error( 'callable', esc_html__( 'Task is not supported action', 'awebooking' ), $task );
		}

		return $callback( false );
	}

	/**
	 * Reset capabilities.
	 *
	 * @param bool $silent Prevent message or not.
	 * @return \stdClass
	 */
	public static function reset_roles( $silent = false ) {
		$roles = new Roles;

		$roles->remove();
		$roles->create();

		return static::response( esc_html__( 'The roles has been reset successfully!', 'awebooking' ), $silent );
	}

	/**
	 * Clear expired transients.
	 *
	 * @param bool $silent Prevent message or not.
	 * @return \stdClass
	 */
	public static function clear_expired_transients( $silent = false ) {
		delete_transient( 'awebooking_premium_themes' );
		delete_transient( 'awebooking_premium_addons' );

		$deleted = abrs_delete_expired_transients();

		/* translators: %d: amount of expired transients */
		return static::response( sprintf( esc_html__( '%d transients rows cleared', 'awebooking' ), $deleted ), $silent );
	}

	/**
	 * Delete all orphan rows in AweBooking tables.
	 *
	 * @param bool $silent Prevent message or not.
	 * @return \stdClass
	 */
	public static function optimize_database( $silent = false ) {
		global $wpdb;

		// Delete orphaned data. @codingStandardsIgnoreStart
		$wpdb->query( "DELETE t1 FROM {$wpdb->prefix}awebooking_rooms AS t1 LEFT JOIN {$wpdb->prefix}posts AS t2 ON t2.ID = t1.id WHERE t2.ID IS NULL" );
		$wpdb->query( "DELETE t1 FROM {$wpdb->prefix}awebooking_pricing AS t1 LEFT JOIN {$wpdb->prefix}posts AS t2 ON t2.ID = t1.rate_id WHERE t2.ID IS NULL" );
		$wpdb->query( "DELETE t1 FROM {$wpdb->prefix}awebooking_booking AS t1 LEFT JOIN {$wpdb->prefix}awebooking_rooms AS t2 ON t2.id = t1.room_id WHERE t2.id IS NULL" );
		$wpdb->query( "DELETE t1 FROM {$wpdb->prefix}awebooking_availability AS t1 LEFT JOIN {$wpdb->prefix}awebooking_rooms AS t2 ON t2.id = t1.room_id WHERE t2.id IS NULL" );
		$wpdb->query( "DELETE t1 FROM {$wpdb->prefix}awebooking_booking_items AS t1 LEFT JOIN {$wpdb->prefix}posts AS t2 ON t2.ID = t1.booking_id WHERE t2.ID IS NULL" );
		$wpdb->query( "DELETE t1 FROM {$wpdb->prefix}awebooking_booking_itemmeta AS t1 LEFT JOIN {$wpdb->prefix}awebooking_booking_items AS t2 ON t2.booking_item_id = t1.booking_item_id WHERE t2.booking_item_id IS NULL" );

		// Delete all rows don't have any data.
		$sum_days = 'd1 + d2 + d3 + d4 + d5 + d6 + d7 + d8 + d9 + d10 + d11 + d12 + d13 + d14 + d15 + d16 + d17 + d18 + d19 + d20 + d21 + d22 + d23 + d24 + d25 + d26 + d27 + d28 + d29 + d30 + d31';
		$wpdb->query( "DELETE FROM {$wpdb->prefix}awebooking_booking WHERE ({$sum_days}) = 0" );
		$wpdb->query( "DELETE FROM {$wpdb->prefix}awebooking_pricing WHERE ({$sum_days}) = 0" );
		$wpdb->query( "DELETE FROM {$wpdb->prefix}awebooking_availability WHERE ({$sum_days}) = 0" );
		// @codingStandardsIgnoreEnd

		return static::response( esc_html__( 'Optimized database!', 'awebooking' ), $silent );
	}

	/**
	 * Delete all orphan rows in AweBooking tables.
	 *
	 * @param bool $silent Prevent message or not.
	 * @return \stdClass|null
	 */
	public static function set_orphan_rooms( $silent = false ) {
		if ( ! abrs_multiple_hotels() ) {
			return null;
		}

		$primary_hotel = abrs_get_page_id( 'primary_hotel' );
		$updated = 0;

		foreach ( abrs_get_orphan_room_types() as $room_id ) {
			$_updated = update_post_meta( $room_id, '_hotel_id', $primary_hotel );

			if ( $_updated ) {
				$updated++;
			}
		}

		return static::response( sprintf( esc_html__( '%d room type(s) has been updated!', 'awebooking' ), $updated ), $silent );
	}

	/**
	 * Response a message.
	 *
	 * @param string $message The message.
	 * @param bool   $silent  Prevent message or not.
	 *
	 * @return \stdClass
	 */
	protected static function response( $message, $silent = false ) {
		$msg = new \stdClass;

		$msg->message = ! $silent ? $message : null;

		return $msg;
	}
}
