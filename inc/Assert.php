<?php
namespace AweBooking;

use OverflowException;
use InvalidArgumentException;
use AweBooking\Model\Guest;
use AweBooking\Model\Room_Type;
use AweBooking\Model\Booking;
use AweBooking\Model\Booking_Item;
use AweBooking\Model\Exceptions\Model_Not_Found_Exception;
use AweBooking\Reservation\Exceptions\Overflow_Guest_Exception;

class Assert {
	/**
	 * Assert a given object must be exists.
	 *
	 * @param  \AweBooking\Model\WP_Object $object WP_Object implementation.
	 * @return void
	 *
	 * @throws Model_Not_Found_Exception
	 */
	public static function object_exists( $object ) {
		if ( is_null( $object ) ) {
			throw new Model_Not_Found_Exception( esc_html__( 'Resource not found', 'awebooking' ) );
		}

		if ( ! $object->exists() ) {
			throw (new Model_Not_Found_Exception)->set_model( get_class( $object ) );
		}
	}

	/**
	 * Assert that a booking_item exists in a booking.
	 *
	 * @param  \AweBooking\Model\Booking_Item $room_item The booking item.
	 * @param  \AweBooking\Model\Booking      $booking   The booking reference.
	 *
	 * @throws \InvalidArgumentException
	 */
	public static function booking_item( Booking_Item $room_item, Booking $booking ) {
		if ( $booking->get_id() !== $room_item->get_booking_id() ) {
			throw new \InvalidArgumentException( esc_html__( 'Invalid booking item data', 'awebooking' ) );
		}
	}

	/**
	 * Assert the guest number.
	 *
	 * @param  \AweBooking\Model\Guest     $guest     The guest.
	 * @param  \AweBooking\Model\Room_Type $room_type The room-type.
	 * @return void
	 *
	 * @throws Overflow_Guest_Exception
	 */
	public static function guest_number( Guest $guest, Room_Type $room_type ) {
		$total_guest = $room_type->is_calculation_infants()
			? $guest->total()
			: $guest->total_without_infants();

		if ( $total_guest > $room_type->get_maximum_occupancy() ) {
			throw new Overflow_Guest_Exception( sprintf(
				/* translators: %1$s: Room type title, %2$d: Maximum occupancy, %3$d: Given occupancy */
				esc_html__( 'The %1$s room can only stay maximum %2$d guest but given %3$d', 'awebooking' ),
				esc_html( $room_type->get_title() ),
				esc_html( $room_type->get_maximum_occupancy() ),
				esc_html( $total_guest )
			) );
		}
	}
}
