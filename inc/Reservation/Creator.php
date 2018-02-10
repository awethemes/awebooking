<?php
namespace AweBooking\Reservation;

use AweBooking\Setting;
use AweBooking\Model\Room;
use AweBooking\Model\Booking;
use AweBooking\Model\Booking_Room_Item;

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
}
