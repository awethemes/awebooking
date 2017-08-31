<?php
namespace AweBooking\Booking\Events;

use AweBooking\Factory;
use AweBooking\AweBooking;
use AweBooking\Hotel\Room;
use Roomify\Bat\Event\Event;
use AweBooking\Support\Period;
use AweBooking\Support\Carbonate;

class Room_State extends Event {
	use Only_Days_Trait;

	/**
	 * Create new instance from Event object.
	 *
	 * @param  Event $event Event object instance.
	 * @return static
	 */
	public static function instance( Event $event ) {
		$room_unit = Factory::get_room_unit( $event->getUnitId() );

		$period = new Period( $event->getStartDate(),
			Carbonate::instance( $event->getEndDate() )->addMinute()
		);

		return new static( $room_unit, $period, $event->getValue() );
	}

	/**
	 * Room state in a period.
	 *
	 * @param Room   $room_unit The room unit instance.
	 * @param Period $period    The period of event.
	 * @param int    $state     The state of event.
	 */
	public function __construct( Room $room_unit, Period $period, $state = AweBooking::STATE_AVAILABLE ) {
		$this->unit    = $room_unit;
		$this->unit_id = $room_unit->getUnitId();
		$this->value   = $state;

		// Here that's why we subtract a minute from end date:
		// We have period from: 2017-10-10 to 2017-10-13, so have "3" nights (10-11, 11-12, 12-13).
		// The BAT system calculate by daily, so if we past args like normal,
		// we will receive result of "4" nights (10, 11, 12, 13).
		$this->end_date   = $period->get_end_date()->subMinute();
		$this->start_date = $period->get_start_date();
	}

	/**
	 * If current state is available.
	 *
	 * @return bool
	 */
	public function is_available() {
		return $this->getValue() === AweBooking::STATE_AVAILABLE;
	}

	/**
	 * If current state is unavailable.
	 *
	 * @return bool
	 */
	public function is_unavailable() {
		return $this->getValue() === AweBooking::STATE_UNAVAILABLE;
	}

	/**
	 * If current state is pending.
	 *
	 * @return bool
	 */
	public function is_pending() {
		return $this->getValue() === AweBooking::STATE_PENDING;
	}

	/**
	 * If current state is booked.
	 *
	 * @return bool
	 */
	public function is_booked() {
		return $this->getValue() === AweBooking::STATE_BOOKED;
	}

	/**
	 * Save current state into the database.
	 *
	 * @return bool
	 */
	public function save() {
		// The booked and pending state cannot be ignored some days.
		if ( $this->is_booked() || $this->is_pending() ) {
			$this->only_days = [];
		}

		return awebooking( 'store.availability' )->storeEvent( $this );
	}
}
