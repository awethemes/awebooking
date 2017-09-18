<?php
namespace AweBooking;

use AweBooking\Support\Service_Hooks;

class Logic_Hooks extends Service_Hooks {
	/**
	 * Init service provider.
	 *
	 * @param AweBooking $awebooking AweBooking instance.
	 */
	public function init( $awebooking ) {
		add_action( 'pre_delete_term', [ $this, 'pre_delete_location' ], 10, 2 );

		add_action( 'delete_post', [ $this, 'delete_room_type' ] );
		add_action( 'before_delete_post', [ $this, 'delete_booking_items' ] );
	}

	/**
	 * Prevent delete default hotel location.
	 *
	 * @param  int    $term     Term ID.
	 * @param  string $taxonomy Taxonomy Name.
	 * @return void
	 */
	public function pre_delete_location( $term, $taxonomy ) {
		if ( AweBooking::HOTEL_LOCATION !== $taxonomy ) {
			return;
		}

		$default_location = absint( awebooking_option( 'location_default' ) );
		if ( $default_location && $default_location === $term ) {
			exit( 1 ); // Prevent delete default location.
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
	 */
	public function delete_booking_items( $postid ) {
		global $wpdb;

		if ( get_post_type( $postid ) !== AweBooking::BOOKING ) {
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

		if ( get_post_type( $postid ) !== AweBooking::ROOM_TYPE ) {
			return;
		}

		// Get all rooms in current room-type.
		$rooms = $wpdb->get_results(
			$wpdb->prepare( "SELECT * FROM `{$wpdb->prefix}awebooking_rooms` WHERE `room_type` = '%d'", $postid ),
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
