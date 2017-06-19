<?php

namespace AweBooking;

use DateTime;
use Carbon\Carbon;
use Roomify\Bat\Event\Event;
use AweBooking\Interfaces\Price as Price_Interface;

class Rate_Pricing extends Event {
	/**
	 * Room state in a time period.
	 *
	 * @param Room     $room       Room object instance.
	 * @param DateTime $start_date Start of date of state.
	 * @param DateTime $end_date   End of date of state.
	 * @param int      $state      State status.
	 */
	public function __construct( Rate $rate, DateTime $start_date, DateTime $end_date, Price_Interface $price ) {
		$this->unit = $rate;
		$this->unit_id = $rate->getUnitId();

		$this->end_date = Carbon::instance( $end_date );
		$this->start_date = Carbon::instance( $start_date );

		$this->value = $price->to_amount();
	}

	/**
	 * Save current state into the database.
	 *
	 * @return boolean
	 */
	public function save() {
		return awebooking( 'store.pricing' )->storeEvent( $this );
	}
}
