<?php
namespace AweBooking\Booking\Events;

use DateTime;
use Carbon\Carbon;
use Roomify\Bat\Event\Event;
use AweBooking\Pricing\Price;
use AweBooking\Support\Traits\BAT_Only_Days;

class Rate_Pricing extends Event {
	use BAT_Only_Days;

	/**
	 * Room state in a time period.
	 *
	 * @param Rate     $rate       Rate object instance.
	 * @param DateTime $start_date Start of date of state.
	 * @param DateTime $end_date   End of date of state.
	 * @param Price    $price      Price object.
	 */
	public function __construct( Rate $rate, DateTime $start_date, DateTime $end_date, Price $price ) {
		$this->unit = $rate;
		$this->unit_id = $rate->getUnitId();

		$this->end_date = Carbon::instance( $end_date );
		$this->start_date = Carbon::instance( $start_date );

		$this->value = $price->to_amount();
	}

	/**
	 * Save current state into the database.
	 *
	 * @return bool
	 */
	public function save() {
		return awebooking( 'store.pricing' )->storeEvent( $this );
	}
}
