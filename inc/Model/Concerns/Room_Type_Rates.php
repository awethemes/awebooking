<?php
namespace AweBooking\Model\Concerns;

use AweBooking\Model\Factory;
use AweBooking\Constants;
use AweBooking\Model\Pricing\Base_Rate;
use AweBooking\Model\Pricing\Standard_Plan;
use AweBooking\Support\Decimal;
use AweBooking\Support\Collection;

trait Room_Type_Rates {
	/**
	 * Cache the base rate.
	 *
	 * @var \AweBooking\Model\Base_Rate
	 */
	protected $base_rate;

	/**
	 * {@inheritdoc}
	 */
	public function get_rates() {
		if ( is_null( $this->rates ) ) {
			$this->rates = apply_filters( 'awebooking/base_rate/rates', $this->setup_rates(), $this );
		}

		return $this->rates;
	}

	/**
	 * Setup the rates.
	 *
	 * @return \AweBooking\Support\Collection
	 */
	protected function setup_rates() {
		$rates = new Collection;

		$rates->push( $this->get_line_rate() );

		return $rates;
	}

	/**
	 * Return a instance of line rates.
	 *
	 * @return \AweBooking\Model\Base_Rate
	 */
	protected function get_line_rate() {
		return new Base_Rate( $this->instance );
	}

	public function get_rate_plans() {
		return new Collection( [ $this->get_standard_rate_plan() ] );
	}

	public function get_standard_rate_plan() {
		return $this->get_base_rate();
	}

	/**
	 * Get the base_rate of this room-type.
	 *
	 * @return \AweBooking\Model\Base_Rate
	 */
	public function get_base_rate() {
		if ( is_null( $this->base_rate ) ) {
			$this->base_rate = apply_filters( $this->prefix( 'get_base_rate' ), new Standard_Plan( $this ), $this );
		}

		return $this->base_rate;
	}

	/**
	 * Determines if a given rate_plan exists.
	 *
	 * @param  \AweBooking\Model\Pricing\Rate_Plan $rate_plan The rate_plan object.
	 * @return boolean
	 */
	public function has_rate_plan( $rate_plan ) {
		// In case a Standard_Plan give, we just check for the same ID.
		if ( $rate_plan instanceof Standard_Plan && $rate_plan->get_id() === $this->get_id() ) {
			return true;
		}

		// TODO: ...
		return false;
	}
}
