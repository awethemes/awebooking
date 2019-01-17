<?php

namespace AweBooking\Core\Providers;

use AweBooking\Constants;
use AweBooking\Model\Room_Type;
use AweBooking\Support\Service_Provider;

class Logic_Service_Provider extends Service_Provider {
	/**
	 * Init service provider.
	 *
	 * @access private
	 */
	public function init() {
		add_action( 'delete_post', [ $this, 'delete_hotel' ] );
		add_action( 'delete_post', [ $this, 'delete_room_type' ] );
		add_action( 'before_delete_post', [ $this, 'delete_booking_items' ] );

		// TODO: ...
		add_action( 'abrs_room_saved', [ $this, 'update_total_rooms' ] );
		add_action( 'abrs_room_deleted', [ $this, 'update_total_rooms' ] );
		add_action( 'abrs_room_type_saved', [ $this, 'update_total_rooms' ] );
	}

	/**
	 * Fire actions after a hotel has been deleted.
	 *
	 * @param  int $postid The hotel ID.
	 * @return void
	 */
	public function delete_hotel( $postid ) {
		if ( get_post_type( $postid ) !== Constants::HOTEL_LOCATION ) {
			return;
		}

		if ( abrs_get_page_id( 'primary_hotel' ) === (int) $postid ) {
			abrs_update_option( 'primary_hotel', null );
		}
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

		if ( ! is_null( $rooms ) && ! empty( $rooms ) ) {
			$unit_ids = implode( ',', array_column( $rooms, 'id' ) );

			// @codingStandardsIgnoreStart
			$wpdb->query( "DELETE FROM `{$wpdb->prefix}awebooking_rooms` WHERE `id` IN ({$unit_ids})" );
			$wpdb->query( "DELETE FROM `{$wpdb->prefix}awebooking_booking` WHERE `room_id` IN ({$unit_ids})" );
			$wpdb->query( "DELETE FROM `{$wpdb->prefix}awebooking_availability` WHERE `room_id` IN ({$unit_ids})" );
			// @codingStandardsIgnoreEnd
		}

		// Delete all `rate` in "awebooking_pricing" table.
		$wpdb->query( $wpdb->prepare( "DELETE FROM `{$wpdb->prefix}awebooking_pricing` WHERE `rate_id` = %d", $postid ) );

		do_action( 'abrs_deleted_room_type', $postid );
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
		if ( get_post_type( $postid ) !== Constants::BOOKING ) {
			return;
		}

		// Get booking object.
		$booking = abrs_get_booking( $postid );

		do_action( 'abrs_delete_booking_items', $postid );

		$booking->remove_items();

		do_action( 'abrs_deleted_booking_items', $postid );
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
}
