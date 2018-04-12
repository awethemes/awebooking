<?php
namespace AweBooking\Reservation;

use AweBooking\Model\Room;
use AweBooking\Model\Room_Type;
use AweBooking\Model\Common\Timespan;
use AweBooking\Model\Common\Guest_Counts;
use AweBooking\Model\Pricing\Rate_Plan;
use AweBooking\Support\Traits\Fluent_Getter;
use AweBooking\Reservation\Pricing\Room_Rate;
use AweBooking\Support\Collection;

class Room_Stay {
	use Fluent_Getter;

	/**
	 * The timespan.
	 *
	 * @var \AweBooking\Model\Common\Timespan
	 */
	protected $timespan;

	/**
	 * The guest counts.
	 *
	 * @var \AweBooking\Model\Common\Guest_Counts
	 */
	protected $guest_counts;

	/**
	 * The booked room-type.
	 *
	 * @var \AweBooking\Model\Room_Type
	 */
	protected $room_type;

	/**
	 * The booked rate-plan.
	 *
	 * @var \AweBooking\Model\Pricing\Rate_Plan
	 */
	protected $rate_plan;

	/**
	 * The room assigned to stay.
	 *
	 * @var \AweBooking\Model\Room
	 */
	protected $assigned_room;

	/**
	 * The room-rate.
	 *
	 * @var \AweBooking\Reservation\Pricing\Room_Rate
	 */
	protected $room_rate;

	/**
	 * Create new room-stay.
	 *
	 * @param \AweBooking\Model\Room_Type           $room_type    The room_type.
	 * @param \AweBooking\Model\Pricing\Rate_Plan   $rate_plan    The rate_plan.
	 * @param \AweBooking\Model\Common\Timespan     $timespan     The timespan.
	 * @param \AweBooking\Model\Common\Guest_Counts $guest_counts The guest_counts.
	 */
	public function __construct( Room_Type $room_type, Rate_Plan $rate_plan, Timespan $timespan, Guest_Counts $guest_counts ) {
		$this->room_type     = $room_type;
		$this->rate_plan     = $rate_plan;
		$this->timespan      = $timespan;
		$this->guest_counts  = $guest_counts;
	}

	/**
	 * Apply a rate price to this room-stay.
	 *
	 * @param  \AweBooking\Reservation\Pricing\Room_Rate $room_rate The room rate.
	 * @return $this
	 */
	public function apply( Room_Rate $room_rate ) {
		$this->room_rate = $room_rate;

		return $this;
	}

	/**
	 * Assign a room.
	 *
	 * @param  \AweBooking\Model\Room $room The room.
	 * @return $this
	 */
	public function assign( Room $room ) {
		$this->assigned_room = $room;

		return $this;
	}

	/**
	 * Get the room-type.
	 *
	 * @return \AweBooking\Model\Room_Type
	 */
	public function get_room_type() {
		return $this->room_type;
	}

	/**
	 * Get the rate_plan.
	 *
	 * @return \AweBooking\Model\Pricing\Rate_Plan
	 */
	public function get_rate_plan() {
		return $this->rate_plan;
	}

	/**
	 * Set the timespan.
	 *
	 * @return \AweBooking\Model\Common\Timespan
	 */
	public function get_timespan() {
		return $this->timespan;
	}

	/**
	 * Get the guest_counts.
	 *
	 * @return \AweBooking\Model\Common\Guest_Counts
	 */
	public function get_guest_counts() {
		return $this->guest_counts;
	}

	/**
	 * Get the assigned_room.
	 *
	 * @return \AweBooking\Model\Room|null
	 */
	public function get_assigned_room() {
		return $this->assigned_room;
	}

	/**
	 * Get the room_rate.
	 *
	 * @return \AweBooking\Reservation\Pricing\Room_Rate|null
	 */
	public function get_room_rate() {
		return $this->assigned_room;
	}
}
