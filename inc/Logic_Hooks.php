<?php
namespace AweBooking;

use AweBooking\AweBooking;
use AweBooking\Support\Period;
use Skeleton\Container\Service_Hooks;

class Logic_Hooks extends Service_Hooks {
	/**
	 * Init service provider.
	 *
	 * This method will be run after container booted.
	 *
	 * @param AweBooking $awebooking AweBooking Container instance.
	 */
	public function init( $awebooking ) {
		add_action( 'save_post', [ $this, 'save_booking' ] );
		add_action( 'deleted_post', [ $this, 'deleted_room_type' ] );
		add_action( 'before_delete_post', [ $this, 'delete_booking' ] );
		add_action( 'pre_delete_term', [ $this, 'pre_delete_term' ], 10, 2 );
	}

	/**
	 * //
	 *
	 * @param  int    $term     Term ID.
	 * @param  string $taxonomy Taxonomy Name.
	 * @return void
	 */
	public function pre_delete_term( $term, $taxonomy ) {
		if ( AweBooking::HOTEL_LOCATION === $taxonomy ) {
			// TODO: ...
		}
	}

	/**
	 * Fire actions after save booking.
	 *
	 * TODO: We need improve this.
	 *
	 * @param  int $postid Current booking ID.
	 * @return void
	 */
	public function save_booking( $postid ) {
		if ( wp_is_post_revision( $postid ) ) {
			return;
		}

		if ( get_post_type( $postid ) !== AweBooking::BOOKING ) {
			return;
		}

		// ...
	}

	/**
	 * Before a booking deleted, we have somethings todo.
	 *
	 * 1. Restore available state of booking room.
	 * 2. Remove booking event in `awebooking_booking` table.
	 * 3. ...
	 *
	 * @param  string $postid The booking ID will be delete.
	 * @return void
	 */
	public function delete_booking( $postid ) {
		if ( get_post_type( $postid ) !== AweBooking::BOOKING ) {
			return;
		}

		// Call this hotel concierge.
		$concierge = awebooking()->make( 'concierge' );

		// First, restore the room state to "available".
		$the_booking  = new Booking( absint( $postid ) );
		$booking_room = $the_booking->get_room_unit();

		if ( $booking_room instanceof Room && $booking_room->exists() ) {
			try {
				$period = new Period( $the_booking['check_in'], $the_booking['check_out'], false );

				$concierge->set_room_state( $booking_room, $period, AweBooking::STATE_AVAILABLE, [ 'force' => true ] );
				$concierge->set_booking_event( $the_booking, $period, [ 'clear' => true ] );

			} catch ( \Exception $e ) {
				// TODO: Log exception error.
			}
		}

		/**
		 * Fire action after a booking deleted.
		 *
		 * @param int $booking_id The booking ID was deleted.
		 */
		do_action( 'awebooking/delete_booking', $postid );
	}

	/**
	 * Fire actions after a room-type deleted.
	 *
	 * In case of AweBooking, we'll fire below actions.
	 *
	 * 1. Delete all rooms.
	 * 2. Delete all `unit` in "awebooking_availability" and "awebooking_booking" table.
	 * 3. Delete all `rate` in "awebooking_pricing" table.
	 * 4. ....
	 *
	 * @param  int $postid The room-type ID deleted.
	 * @return void
	 */
	public function deleted_room_type( $postid ) {
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

		/**
		 * Fire action after a room-type deleted.
		 *
		 * @param int $room_type_id The room-type ID was deleted.
		 */
		do_action( 'awebooking/deleted_room_type', $postid );
	}
}
