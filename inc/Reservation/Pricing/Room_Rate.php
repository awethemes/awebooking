<?php
namespace AweBooking\Reservation\Pricing;

use AweBooking\Support\Collection;
use AweBooking\Model\Pricing\Rate;

class Room_Rate {
	/**
	 * The room rate.
	 *
	 * @var \AweBooking\Model\Pricing\Rate
	 */
	protected $rate;

	/**
	 * The addition rates.
	 *
	 * @var \AweBooking\Support\Collection
	 */
	protected $addition_rates;

	/**
	 * Constructor.
	 *
	 * @param \AweBooking\Model\Pricing\Rate|null $rate           The rate.
	 * @param array                               $addition_rates The addition_rates.
	 */
	public function __construct( Rate $rate = null, $addition_rates = [] ) {
		$this->rate = $rate;
		$this->addition_rates = Collection::make( $addition_rates );
	}

	/**
	 * Sets the room rate.
	 *
	 * @param  \AweBooking\Model\Pricing\Rate $rate The rate.
	 * @return $this
	 */
	public function select( Rate $rate ) {
		$this->rate = $rate;

		return $this;
	}

	/**
	 * Push a rate into the addition_rates.
	 *
	 * @param  \AweBooking\Model\Pricing\Rate $rate The rate.
	 * @return $this
	 */
	public function addition( Rate $rate ) {
		$this->addition_rates->push( $rate );

		return $this;
	}

	/**
	 * Gets the room rate.
	 *
	 * @return \AweBooking\Model\Pricing\Rate
	 */
	public function get_rate() {
		return $this->rate;
	}

	/**
	 * Gets the addition rates.
	 *
	 * @return \AweBooking\Support\Collection
	 */
	public function get_addition_rates() {
		return $this->addition_rates;
	}

	/**
	 * Empty the addition_rates.
	 *
	 * @return void
	 */
	public function flush_addition_rates() {
		$this->addition_rates = new Collection;
	}
}
