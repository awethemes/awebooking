<?php
namespace AweBooking\Reservation;

use AweBooking\Setting;
use AweBooking\Model\Room;
use AweBooking\Model\Booking;
use AweBooking\Model\Booking_Room_Item;
use AweBooking\Calendar\Calendar;
use AweBooking\Calendar\Resource\Resource;
use AweBooking\Calendar\Provider\State_Provider;
use AweBooking\Calendar\Provider\Booking_Provider;

class Creator {
	/**
	 * Create new booking room item from reservation item.
	 *
	 * @param  \AweBooking\Reservation\Item $item    The reservation item.
	 * @param  \AweBooking\Model\Booking    $booking Optional, the Booking this item belongs to.
	 * @return \AweBooking\Model\Booking_Room_Item
	 */
	public function create_booking_room( Item $item, Booking $booking = null ) {
		$room_item = new Booking_Room_Item;

		$room_item->fill([
			'name'         => $item->get_label(),
			'booking_id'   => $booking ? $booking->get_id() : null,
			'room_id'      => $item->room->get_id(),
			'check_in'     => $item->stay->get_check_in()->toDateString(),
			'check_out'    => $item->stay->get_check_out()->toDateString(),

			'adults'       => $item->guest->get_adults(),
			'children'     => awebooking( 'setting' )->is_children_bookable() ? $item->guest->get_children() : null,
			'infants'      => awebooking( 'setting' )->is_infants_bookable() ? $item->guest->get_infants() : null,

			'subtotal'     => 0, // Pre-discount.
			'total'        => 0,
		]);

		return $room_item;
	}

	/**
	 * Create the state calendar by given a room_unit.
	 *
	 * @param  \AweBooking\Model\Room|init $room_unit The room unit.
	 * @return \AweBooking\Calendar\Calendar
	 */
	public function create_state_calendar( $room_unit ) {
		$resource = static::fillter_resource( $room_unit );

		return new Calendar( $resource, new State_Provider( [ $resource ] ) );
	}

	/**
	 * Create the state calendar by given a room_unit.
	 *
	 * @param  \AweBooking\Model\Room|init $room_unit The room unit.
	 * @return \AweBooking\Calendar\Calendar
	 */
	public function create_booking_calendar( $room_unit ) {
		$resource = static::fillter_resource( $room_unit );

		return new Calendar( $resource, new Booking_Provider( [ $resource ] ) );
	}

	/**
	 * Fillter the calendar resource.
	 *
	 * @param  mixed $resource The resource.
	 * @return int
	 */
	protected static function fillter_resource( $resource ) {
		if ( $resource instanceof Resource ) {
			return $resource;
		}

		$resource = method_exists( $resource, 'get_id' )
			? $resource->get_id()
			: absint( $resource );

		return new Resource( $resource );
	}
}
