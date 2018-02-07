<?php
namespace AweBooking\Reservation\Pricing;

use AweBooking\Support\Decimal;
use AweBooking\Support\Carbonate;
use AweBooking\Calendar\Period\Day;
use AweBooking\Calendar\Period\Period;

class Night extends Period {
	/**
	 * The night amount.
	 *
	 * @var \AweBooking\Support\Decimal
	 */
	protected $amount;

	/**
	 * Represent for a night with a amount.
	 *
	 * @param DateTime|string $night  The night period.
	 * @param Decimal         $amount The amount.
	 */
	public function __construct( $night, Decimal $amount ) {
		$this->amount = $amount;

		if ( $night instanceof Day ) {
			$night = $night->get_start_date();
		} else {
			$night = Carbonate::create_datetime( $night );
		}

		// Adjust the night time begin.
		$night = $night->setTime( 14, 0, 0 );

		// The night standard will be from 14:00 to 12:00 (noon) of next day.
		parent::__construct( $night, $night->copy()->addHours( 22 ) );
	}

	/**
	 * Get the amount of night.
	 *
	 * @return \AweBooking\Support\Decimal
	 */
	public function get_amount() {
		return $this->amount;
	}

	/**
	 * Adjust the amount.
	 *
	 * @param \AweBooking\Support\Decimal $amount The Decimal amount.
	 */
	public function adjust( Decimal $amount ) {
		$this->amount = $amount;
	}

	/**
	 * {@inheritdoc}
	 */
	public function __toString() {
		return $this->amount->as_string();
	}
}
