<?php
namespace AweBooking\Reservation\Search;

use AweBooking\Constants;
use AweBooking\Model\Room_Type;

class Pricing {
	/**
	 * The room type instance.
	 *
	 * @var \AweBooking\Model\Room_Type
	 */
	protected $room_type;

	/**
	 * The rates availability.
	 *
	 * @var \AweBooking\Calendar\Finder\Response
	 */
	protected $rates;

	/**
	 * Cache the pricing.
	 *
	 * @var array
	 */
	protected $pricing;

	/**
	 * Constructor.
	 *
	 * @param \AweBooking\Model\Room_Type                 $room_type The room type.
	 * @param \AweBooking\Reservation\Search\Availability $rates     The rates availability.
	 */
	public function __construct( Room_Type $room_type, Availability $rates ) {
		$this->room_type = $room_type;
		$this->rates     = $rates;
	}

	/**
	 * Gets the price.
	 *
	 * @return \AweBooking\Support\Decimal
	 */
	public function get_price() {
		$this->setup_pricing();

		// Something went wrong, just return a zero.
		if ( is_null( $this->pricing ) ) {
			return abrs_decimal( 0 );
		}

		return apply_filters( 'awebooking/reservation/rate_price', $this->pricing[0], $this );
	}

	/**
	 * Gets the price as breakdown.
	 *
	 * @return \AweBooking\Support\Collection
	 */
	public function get_breakdown() {
		$this->setup_pricing();

		// Something went wrong, just mock of breakdown.
		if ( is_null( $this->pricing ) ) {
			return abrs_collect();
		}

		return apply_filters( 'awebooking/reservation/rate_breakdown', $this->pricing[1], $this );
	}

	/**
	 * Setup the pricing.
	 *
	 * @return void
	 */
	protected function setup_pricing() {
		if ( ! is_null( $this->pricing ) ) {
			return;
		}

		// Leave if empty rates remains.
		if ( 0 === count( $this->rates->remains() ) ) {
			return;
		}

		// Get back the reservaion request.
		$request = $this->rates->get_request();

		// Select the rate pricing.
		// By default we will select the first item matches.
		$rate = apply_filters( 'awebooking/reservation/select_rate_pricing',
			$this->rates->select( 'first' ), $request, $this->room_type, $this->rates
		);

		// Retrieve the pricing.
		$pricing = abrs_retrieve_price([
			'rate'        => $rate,
			'timespan'    => $request->get_timespan(),
			'granularity' => Constants::GL_NIGHTLY,
		]);

		if ( ! is_wp_error( $pricing ) ) {
			$this->pricing = $pricing;
		}
	}
}
