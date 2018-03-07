<?php
namespace AweBooking\Model\Traits\Room_Type;

use AweBooking\Factory;
use AweBooking\Constants;
use AweBooking\Model\Base_Rate;
use AweBooking\Model\Contracts\Rate_Plan;
use AweBooking\Support\Decimal;
use AweBooking\Support\Collection;

trait Rate_Plans_Trait {
	/**
	 * Cache the base rate.
	 *
	 * @var \AweBooking\Model\Base_Rate
	 */
	protected $base_rate;

	public function get_rate_plans() {
	}

	/**
	 * Get the base_rate of this room-type.
	 *
	 * @return \AweBooking\Model\Base_Rate
	 */
	public function get_base_rate() {
		if ( is_null( $this->base_rate ) ) {
			$this->base_rate = apply_filters( $this->prefix( 'get_base_rate' ), new Base_Rate( $this ), $this );
		}

		return $this->base_rate;
	}

	/**
	 * Determines if a given rate_plan exists.
	 *
	 * @param  \AweBooking\Model\Contracts\Rate_Plan $rate_plan The rate_plan object.
	 * @return boolean
	 */
	public function has_rate_plan( Rate_Plan $rate_plan ) {
		// In case a Base_Rate give, we just check for the same ID.
		if ( $rate_plan instanceof Base_Rate && $rate_plan->get_id() === $this->get_id() ) {
			return true;
		}

		// TODO: ...
		return false;
	}
}
