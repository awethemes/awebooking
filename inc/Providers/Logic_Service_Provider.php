<?php
namespace AweBooking\Providers;

use AweBooking\Constants;
use AweBooking\Model\Room_Type;
use AweBooking\Support\Service_Provider;

class Logic_Service_Provider extends Service_Provider {
	/**
	 * Init service provider.
	 *
	 * @param AweBooking $awebooking AweBooking instance.
	 */
	public function init( $awebooking ) {
		add_action( 'delete_post', [ $this, 'delete_room_type' ] );
		add_action( 'before_delete_post', [ $this, 'delete_booking_items' ] );

		add_action( 'awebooking/room/saved', [ $this, 'update_total_rooms' ] );
		add_action( 'awebooking/room_type/saved', [ $this, 'update_total_rooms' ] );
	}

	/**
	 * Perform update total rooms of room type.
	 *
	 * @param  mixed $object The object model.
	 * @access private
	 */
	public function update_total_rooms( $object ) {
		if ( $object instanceof Room_Type ) {
			$room_type = $object;
		} else {
			$room_type = abrs_get_room_type( $object['room_type'] );
		}

		if ( $room_type && $room_type->exists() ) {
			$room_type->update_meta( '_cache_total_rooms', count( $room_type->get_rooms() ) );
		}
	}

	/**
	 * Before a booking deleted, we have somethings todo.
	 *
	 * 1. Restore available state of booking room.
	 * 2. Remove booking event in `awebooking_booking` table.
	 *
	 * @param  string $postid The booking ID will be delete.
	 * @return void
	 *
	 * @access private
	 */
	public function delete_booking_items( $postid ) {
		global $wpdb;

		if ( get_post_type( $postid ) !== Constants::BOOKING ) {
			return;
		}

		// Get booking object.
		$booking = Factory::get_booking( $postid );

		do_action( 'awebooking/delete_booking_items', $postid );

		// Loop all item and run delete.
		foreach ( $booking->get_all_items() as $item ) {
			$item->delete();
		}

		do_action( 'awebooking/deleted_booking_items', $postid );
	}

	/**
	 * Fire actions after a room-type deleted.
	 *
	 * 1. Delete all rooms.
	 * 2. Delete all `unit` in "awebooking_availability" and "awebooking_booking" table.
	 * 3. Delete all `rate` in "awebooking_pricing" table.
	 *
	 * @param  int $postid The room-type ID deleted.
	 * @return void
	 */
	public function delete_room_type( $postid ) {
		global $wpdb;

		if ( get_post_type( $postid ) !== Constants::ROOM_TYPE ) {
			return;
		}

		// Get all rooms in current room-type.
		$rooms = $wpdb->get_results(
			$wpdb->prepare( "SELECT * FROM `{$wpdb->prefix}awebooking_rooms` WHERE `room_type` = %d", $postid ),
			ARRAY_A
		);

		// Make sure we don't get query error.
		if ( ! is_null( $rooms ) && ! empty( $rooms ) ) {
			$unit_ids = implode( ',', wp_list_pluck( $rooms, 'id' ) );

			// @codingStandardsIgnoreStart
			$wpdb->query( "DELETE FROM `{$wpdb->prefix}awebooking_rooms` WHERE `id` IN ({$unit_ids})" );
			$wpdb->query( "DELETE FROM `{$wpdb->prefix}awebooking_booking` WHERE `room_id` IN ({$unit_ids})" );
			$wpdb->query( "DELETE FROM `{$wpdb->prefix}awebooking_availability` WHERE `room_id` IN ({$unit_ids})" );
			// @codingStandardsIgnoreEnd
		}

		// Delete all `rate` in "awebooking_pricing" table.
		$wpdb->query( $wpdb->prepare( "DELETE FROM `{$wpdb->prefix}awebooking_pricing` WHERE `rate_id` = %d", $postid ) );

		do_action( 'awebooking/delete_room_type', $postid );
	}
}
