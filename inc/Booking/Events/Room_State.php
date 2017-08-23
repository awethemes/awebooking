<?php
namespace AweBooking\Booking\Events;

use DateTime;
use AweBooking\Factory;
use AweBooking\AweBooking;
use AweBooking\Hotel\Room;
use Roomify\Bat\Event\Event;
use AweBooking\Support\Carbonate;
use AweBooking\Support\Traits\BAT_Only_Days;

class Room_State extends Event {
	use BAT_Only_Days;

	/**
	 * Create new instance from Event object.
	 *
	 * @param  Event $event Event object instance.
	 * @return static
	 */
	public static function instance( Event $event ) {
		return new static(
			Factory::get_room_unit( $event->getUnitId() ),
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
	public function __construct( Room $room, DateTime $start_date, DateTime $end_date, $state = AweBooking::STATE_AVAILABLE ) {
		$this->unit = $room;
		$this->unit_id = $room->getUnitId();

		$this->end_date = Carbonate::instance( $end_date );
		$this->start_date = Carbonate::instance( $start_date );

		$this->value = $state;
	}

	/**
	 * If current state is booked.
	 *
	 * @return bool
	 */
	public function is_booked() {
		return $this->getValue() === static::BOOKED;
	}

	/**
	 * If current state is available.
	 *
	 * @return bool
	 */
	public function is_available() {
		return $this->getValue() === static::AVAILABLE;
	}

	/**
	 * If current state is pending.
	 *
	 * @return bool
	 */
	public function is_pending() {
		return $this->getValue() === static::PENDING;
	}

	/**
	 * If current state is unavailable.
	 *
	 * @return bool
	 */
	public function is_unavailable() {
		return $this->getValue() === static::UNAVAILABLE;
	}

	/**
	 * Save current state into the database.
	 *
	 * @return bool
	 */
	public function save() {
		return awebooking( 'store.availability' )->storeEvent( $this );
	}
}
