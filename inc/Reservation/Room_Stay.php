<?php
namespace AweBooking\Reservation;

use AweBooking\Model\Stay;
use AweBooking\Model\Guest;
use AweBooking\Model\Room_Type;
use AweBooking\Model\Contracts\Rate_Plan;

class Room_Stay {
	/**
	 * The stay period.
	 *
	 * @var \AweBooking\Model\Stay
	 */
	protected $stay;

	/**
	 * The number of guest.
	 *
	 * @var \AweBooking\Model\Guest
	 */
	protected $guest;

	/**
	 * The booked room-type.
	 *
	 * @var \AweBooking\Model\Room_Type
	 */
	protected $room_type;

	/**
	 * The booked rate-plan.
	 *
	 * @var \AweBooking\Model\Contracts\Rate_Plan
	 */
	protected $rate_plan;

	/**
	 * Create new room stay (represent for a booking item).
	 *
	 * @param \AweBooking\Model\Stay                $stay      The stay instance.
	 * @param \AweBooking\Model\Guest               $guest     The guest instance.
	 * @param \AweBooking\Model\Room_Type           $room_type The room_type instance.
	 * @param \AweBooking\Model\Contracts\Rate_Plan $rate_plan The rate_plan instance.
	 */
	public function __construct( Stay $stay, Guest $guest, Room_Type $room_type, Rate_Plan $rate_plan ) {
		$this->stay = $stay;
		$this->guest = $guest;
		$this->room_type = $room_type;
		$this->rate_plan = $rate_plan;
	}

	/**
	 * Set the stay.
	 *
	 * @return \AweBooking\Model\Stay
	 */
	public function get_stay() {
		return $this->stay;
	}

	/**
	 * Get the guest instance.
	 *
	 * @return \AweBooking\Model\Guest
	 */
	public function get_guest() {
		return $this->guest;
	}

	/**
	 * Get the room-type instance.
	 *
	 * @return \AweBooking\Model\Room_Type
	 */
	public function get_room_type() {
		return $this->room_type;
	}

	/**
	 * Get the rate_plan instance.
	 *
	 * @return \AweBooking\Model\Contracts\Rate_Plan
	 */
	public function get_rate_plan() {
		return $this->rate_plan;
	}

	/**
	 * Magic getter method.
	 *
	 * @param  string $property The property name.
	 * @return mixed
	 */
	public function __get( $property ) {
		if ( method_exists( $this, $method = "get_{$property}" ) ) {
			return $this->{$method}();
		}
	}
}
