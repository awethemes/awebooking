<?php
namespace AweBooking\Reservation\Pricing;

use AweBooking\Ruler\Rule;
use AweBooking\Model\Room_Type;
use AweBooking\Model\Pricing\Rate_Plan;
use AweBooking\Reservation\Request;
use AweBooking\Support\Collection;

class Selector {
	/**
	 * The reservation request.
	 *
	 * @var \AweBooking\Reservation\Request
	 */
	protected $request;

	/**
	 * List rates passed the restrictions.
	 *
	 * @var \AweBooking\Support\Collection
	 */
	protected $passed_rates;

	/**
	 * Constructor.
	 *
	 * @param \AweBooking\Reservation\Request $request The reservation request.
	 */
	public function __construct( Request $request ) {
		$this->request = $request;
		$this->passed_rates = new Collection;
	}

	/**
	 * Get the request.
	 *
	 * @return \AweBooking\Reservation\Request
	 */
	public function get_request() {
		return $this->request;
	}

	/**
	 * Get the passed_rates.
	 *
	 * @return \AweBooking\Support\Collection
	 */
	public function get_passed_rates() {
		return $this->passed_rates;
	}

	/**
	 * Perform the select room-rate by given.
	 *
	 * @param  \AweBooking\Model\Room_Type              $room_type The room-type.
	 * @param  \AweBooking\Model\Pricing\Rate_Plan|null $rate_plan The rate-plan.
	 * @return \AweBooking\Reservation\Pricing\Room_Rate|null
	 */
	public function select( Room_Type $room_type, Rate_Plan $rate_plan = null ) {
		// If no rate plan given, use standard rate plan instead.
		$rate_plan = $rate_plan ?: $room_type->get_standard_rate_plan();

		// Get all rates of their rate-plan.
		$rates = $room_type->get_rates( $rate_plan );

		// Filter passed rates.
		$this->perform_filter_rates( $rates );

		// None of any rates passed, leave.
		if ( $this->passed_rates->isEmpty() ) {
			return;
		}

		$selected = $this->passed_rates->first();
		$selected = apply_filters( 'awebooking/pricing/rate_selecting', $selected, $this, $room_type, $rate_plan );

		$room_rate = new Room_Rate( $selected );

		return $room_rate;
	}

	protected function perform_filter_rates( $rates ) {
		$context = $this->get_context();

		$rates->each( function ( $rate ) {
			if ( $rate->apply( $context ) ) {
				$this->passed_rates[] = $rate;
			}
		});
	}
}
