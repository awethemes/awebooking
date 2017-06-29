<?php
namespace AweBooking\Interfaces;

use AweBooking\Rate;
use AweBooking\Room;
use AweBooking\Room_Type;
use AweBooking\Support\Date_Period;

interface Concierge {
	/**
	 * Get room price by booking request.
	 *
	 * @param  Room_Type       $room_type Room type instance.
	 * @param  Booking_Request $request Booking request instance.
	 * @return Price
	 */
	public function get_room_price( Room_Type $room_type, Booking_Request $request );

	/**
	 * Set price for room (by rate).
	 *
	 * @param  Rate        $rate    The rate instance.
	 * @param  Date_Period $period  Date period instance.
	 * @param  Price       $amount  The price instance.
	 * @param  array       $options Price setting options.
	 * @return boolean
	 */
	public function set_room_price( Rate $rate, Date_Period $period, Price $amount, array $options = [] );

	/**
	 * Set the room state.
	 *
	 * @param  Room        $room    The Room instance.
	 * @param  Date_Period $period  Date period instance.
	 * @param  integer     $state   Room state, default is Room_State::UNAVAILABLE.
	 * @param  array       $options Setting options.
	 * @return boolean
	 */
	public function set_room_state( Room $room, Date_Period $period, $state = Room_State::UNAVAILABLE, array $options = [] );

	/**
	 * Check availability for a booking request.
	 *
	 * @param  Booking_Request $request Booking request.
	 * @return array Availability[]
	 */
	public function check_availability( Booking_Request $request );

	/**
	 * Check availability a booking request only in a room type.
	 *
	 * @param  Room_Type       $room_type Room type instance.
	 * @param  Booking_Request $request   Booking request instance.
	 * @return Availability
	 */
	public function check_room_type_availability( Room_Type $room_type, Booking_Request $request );

	/**
	 * Make a booking.
	 *
	 * @param  Availability $request Availability instance.
	 * @return mixed
	 */
	public function make_booking( Availability $request );

	/**
	 * Call limousines car.
	 *
	 * @return void
	 */
	public function call_limousines();
}
