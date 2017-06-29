<?php
namespace AweBooking;

use DateTime;
use Carbon\Carbon;
use Roomify\Bat\Event\Event;

class Room_State extends Event {
	/* State */
	const AVAILABLE   = 0;
	const UNAVAILABLE = 1;
	const PENDING     = 2;
	const BOOKED      = 3;

	/**
	 * Set only days.
	 *
	 * @var array
	 */
	protected $only_days = [];

	/**
	 * Create new instance from Event object.
	 *
	 * @param  Event $event Event object instance.
	 * @return static
	 */
	public static function instance( Event $event ) {
		return new static(
			new Room( $event->getUnitId() ),
			$event->getStartDate(),
			$event->getEndDate(),
			$event->getValue()
		);
	}

	/**
	 * Room state in a time period.
	 *
	 * @param Room     $room       Room object instance.
	 * @param DateTime $start_date Start of date of state.
	 * @param DateTime $end_date   End of date of state.
	 * @param int      $state      State status.
	 */
	public function __construct( Room $room, DateTime $start_date, DateTime $end_date, $state = Room_State::AVAILABLE ) {
		$this->unit = $room;
		$this->unit_id = $room->getUnitId();

		$this->end_date = Carbon::instance( $end_date );
		$this->start_date = Carbon::instance( $start_date );

		$this->value = $state;
	}

	public function get_only_days() {
		return $this->only_days; // Ex: [0, 1, 3, 4, 5, 6].
	}

	public function set_only_days( $days ) {
		$this->only_days = $days;
	}

	/**
	 * If current state is booked.
	 *
	 * @return boolean
	 */
	public function is_booked() {
		return $this->getValue() === static::BOOKED;
	}

	/**
	 * If current state is available.
	 *
	 * @return boolean
	 */
	public function is_available() {
		return $this->getValue() === static::AVAILABLE;
	}

	/**
	 * If current state is pending.
	 *
	 * @return boolean
	 */
	public function is_pending() {
		return $this->getValue() === static::PENDING;
	}

	/**
	 * If current state is unavailable.
	 *
	 * @return boolean
	 */
	public function is_unavailable() {
		return $this->getValue() === static::UNAVAILABLE;
	}

	/**
	 * Save current state into the database.
	 *
	 * @return boolean
	 */
	public function save() {
		return awebooking( 'store.availability' )->storeEvent( $this );
	}
}
