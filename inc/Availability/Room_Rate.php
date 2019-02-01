<?php

namespace AweBooking\Availability;

use AweBooking\Constants;
use AweBooking\Model\Room_Type;
use AweBooking\Model\Pricing\Contracts\Rate;
use AweBooking\Model\Pricing\Contracts\Rate_Interval;
use AweBooking\Support\Traits\Fluent_Getter;

class Room_Rate {
	use Fluent_Getter,
		Deprecated\Room_Rate;

	/**
	 * The res request.
	 *
	 * @var \AweBooking\Availability\Request
	 */
	protected $request;

	/**
	 * The room type instance.
	 *
	 * @var \AweBooking\Model\Room_Type
	 */
	protected $room_type;

	/**
	 * The rate instance.
	 *
	 * @var \AweBooking\Model\Pricing\Contracts\Rate
	 */
	protected $rate_plan;

	/**
	 * The room availability.
	 *
	 * @var \AweBooking\Availability\Availability
	 */
	protected $rooms_availability;

	/**
	 * The filtered rates.
	 *
	 * @var \AweBooking\Availability\Availability
	 */
	protected $rates_availability;

	/**
	 * The rate to retrieve the room price.
	 *
	 * @var \AweBooking\Model\Pricing\Contracts\Rate_Interval
	 */
	protected $room_rate;

	/**
	 * Store the breakdown of room rate.
	 *
	 * @var \AweBooking\Model\Pricing\Breakdown
	 */
	protected $breakdown;

	/**
	 * The additional rates add to to room cost.
	 *
	 * @var array
	 */
	protected $additional_rates = [];

	/**
	 * The additional rates breakdown.
	 *
	 * @var array
	 */
	protected $additional_breakdowns = [];

	/**
	 * Store the calculated prices.
	 *
	 * @var array
	 */
	protected $prices = [
		'room_only'        => 0,
		'additionals'      => 0,
		'rate'             => 0,
		'rate_average'     => 0,
		'rate_first_night' => 0,
	];

	/**
	 * Constructor.
	 *
	 * @param \AweBooking\Availability\Request         $request   The res request.
	 * @param \AweBooking\Model\Room_Type              $room_type The room type instance.
	 * @param \AweBooking\Model\Pricing\Contracts\Rate $rate_plan The rate instance.
	 */
	public function __construct( Request $request, Room_Type $room_type, Rate $rate_plan ) {
		$this->request   = $request;
		$this->room_type = $room_type;
		$this->rate_plan = $rate_plan;
		$this->precheck();
	}

	/**
	 * Setup the rooms availability and pricing.
	 *
	 * @return void
	 */
	public function setup() {
		$constraints = $this->request->get_constraints();

		// First, check the rooms availability.
		$room_response = abrs_check_room_states( $this->room_type->get_rooms(), $this->get_timespan(), Constants::STATE_AVAILABLE, $constraints );
		$this->rooms_availability = new Availability( $this->room_type, $room_response );

		// Check the rates availability.
		$rate_response = abrs_filter_rate_intervals( $this->rate_plan->get_rate_intervals(), $this->get_timespan() );
		$this->rates_availability = new Availability( $this->rate_plan, $rate_response );

		if ( count( $this->rates_availability->remains() ) > 0 ) {
			$this->using( apply_filters( 'abrs_select_room_rate', $this->rates_availability->select(), $this->rates_availability, $this ) );

			do_action( 'abrs_setup_room_rate', $this );

			$this->calculate_costs();
		}
	}

	/**
	 * Pre-validate the the request.
	 *
	 * @return void
	 */
	protected function precheck() {
		$this->request->get_timespan()->requires_minimum_nights( 1 );

		if ( $this->request->get_guest_counts()->get_totals() > $this->room_type->get( 'maximum_occupancy' ) ) {
			throw new \RuntimeException( esc_html__( 'Error: maximum occupancy.', 'awebooking' ) );
		}

		do_action( 'abrs_precheck_room_rate', $this );
	}

	/**
	 * Gets the res request instance.
	 *
	 * @return \AweBooking\Availability\Request
	 */
	public function get_request() {
		return $this->request;
	}

	/**
	 * Gets the room type instance.
	 *
	 * @return \AweBooking\Model\Room_Type
	 */
	public function get_room_type() {
		return $this->room_type;
	}

	/**
	 * Gets the rate instance.
	 *
	 * @return \AweBooking\Model\Pricing\Contracts\Rate
	 */
	public function get_rate_plan() {
		return $this->rate_plan;
	}

	/**
	 * Gets the rooms availability instance.
	 *
	 * @return \AweBooking\Availability\Availability|null
	 */
	public function get_availability() {
		return $this->rooms_availability;
	}

	/**
	 * Gets the rates availability instance.
	 *
	 * @return \AweBooking\Availability\Availability|null
	 */
	public function get_rates_availability() {
		return $this->rates_availability;
	}

	/**
	 * Gets the timespan.
	 *
	 * @return \AweBooking\Model\Common\Timespan
	 */
	public function get_timespan() {
		return $this->request->get_timespan();
	}

	/**
	 * Gets the guest counts.
	 *
	 * @return \AweBooking\Model\Common\Guest_Counts
	 */
	public function get_guest_counts() {
		return $this->request->get_guest_counts();
	}

	/**
	 * Determines if the room rate is visible or not.
	 *
	 * @return bool
	 */
	public function is_visible() {
		if ( count( $this->get_remain_rooms() ) === 0 ) {
			return false;
		}

		if ( is_null( $this->room_rate ) || $this->get_rate() <= 0 ) {
			return false;
		}

		return apply_filters( 'abrs_room_rate_visibility', true, $this );
	}

	/**
	 * Gets the remain rooms.
	 *
	 * @return \AweBooking\Support\Collection
	 */
	public function get_remain_rooms() {
		return $this->rooms_availability ? $this->rooms_availability->remains() : abrs_collect();
	}

	/**
	 * Gets the reject rooms.
	 *
	 * @return \AweBooking\Support\Collection
	 */
	public function get_reject_rooms() {
		return $this->rooms_availability ? $this->rooms_availability->excludes() : abrs_collect();
	}

	/**
	 * Sets the room rate (the room price).
	 *
	 * @param  \AweBooking\Model\Pricing\Contracts\Rate_Interval $rate The rate instance.
	 * @return $this
	 */
	public function using( Rate_Interval $rate ) {
		if ( ! $this->rates_availability->remain( $rate->get_id() ) ) {
			throw new \InvalidArgumentException( esc_html__( 'Invalid single rate.', 'awebooking' ) );
		}

		$breakdown = $rate->get_breakdown( $this->request->get_timespan() );
		if ( is_wp_error( $breakdown ) ) {
			throw new \RuntimeException( $breakdown->get_error_message() );
		}

		$this->room_rate = $rate;
		$this->breakdown = $breakdown;

		$this->calculate_costs();

		return $this;
	}

	/**
	 * Add a additional rate.
	 *
	 * @param  \AweBooking\Model\Pricing\Contracts\Rate_Interval $rate   The rate instance.
	 * @param  string                                            $reason The reason message.
	 * @return $this
	 */
	public function additional( Rate_Interval $rate, $reason = '' ) {
		$key = $rate->get_id();

		if ( is_null( $this->room_rate ) ) {
			throw new \InvalidArgumentException( 'Do it wrong' );
		}

		if ( $this->room_rate->get_id() === $key ) {
			throw new \InvalidArgumentException( 'Can not add a duplicate rate.' );
		}

		$breakdown = $rate->get_breakdown( $this->request->get_timespan() );
		if ( is_wp_error( $breakdown ) ) {
			throw new \RuntimeException( $breakdown->get_error_message() );
		}

		$this->additional_rates[ $key ]      = compact( 'reason', 'rate' );
		$this->additional_breakdowns[ $key ] = $breakdown;
		$this->calculate_costs();

		return $this;
	}

	/**
	 * Perform calculate the prices.
	 *
	 * @return void
	 */
	public function calculate_costs() {
		if ( null === $this->room_rate ) {
			return;
		}

		$room_cost        = $this->breakdown->sum();
		$rate_average     = $this->breakdown->avg();
		$rate_first_night = $this->breakdown->first();
		$additional_cost  = 0;

		foreach ( $this->additional_breakdowns as $_breakdown ) {
			$additional_cost  += $_breakdown->sum();
			$rate_first_night += $_breakdown->avg();
			$rate_average     += $_breakdown->first();
		}

		$this->prices = apply_filters( 'abrs_room_rate_prices', [
			'room_only'        => $room_cost,
			'additionals'      => $additional_cost,
			'rate'             => $room_cost + $additional_cost,
			'rate_average'     => $rate_average,
			'rate_first_night' => $rate_first_night,
		], $this );
	}

	/**
	 * Gets the room rate.
	 *
	 * @return \AweBooking\Model\Pricing\Contracts\Rate_Interval|null
	 */
	public function get_room_rate() {
		return $this->room_rate;
	}

	/**
	 * Gets the rate breakdown.
	 *
	 * @return \AweBooking\Model\Pricing\Breakdown
	 */
	public function get_breakdown() {
		return $this->breakdown;
	}

	/**
	 * Gets the additional rates.
	 *
	 * @return array
	 */
	public function get_additional_rates() {
		return $this->additional_rates;
	}

	/**
	 * Gets the additional breakdowns.
	 *
	 * @return array
	 */
	public function get_additional_breakdowns() {
		return $this->additional_breakdowns;
	}

	/**
	 * Gets the rate (total).
	 *
	 * @return float
	 */
	public function get_rate() {
		return $this->prices['rate'];
	}

	/**
	 * Gets the rate.
	 *
	 * @param  string $type The price type.
	 * @return float
	 */
	public function get_price( $type = 'rate' ) {
		return array_key_exists( $type, $this->prices )
			? $this->prices[ $type ]
			: 0;
	}

	/**
	 * Returns the prices.
	 *
	 * @return array
	 */
	public function get_prices() {
		return $this->prices;
	}
}
