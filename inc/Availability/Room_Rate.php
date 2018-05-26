<?php
namespace AweBooking\Availability;

use WP_Error;
use AweBooking\Constants;
use AweBooking\Model\Room_Type;
use AweBooking\Model\Pricing\Rate;
use AweBooking\Model\Pricing\Rate_Plan;
use AweBooking\Support\Traits\Fluent_Getter;

class Room_Rate {
	use Fluent_Getter;

	/**
	 * The res request.
	 *
	 * @var \AweBooking\Availability\Request
	 */
	protected $request;

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
	 * The errors logging.
	 *
	 * @var \WP_Error
	 */
	protected $errors;

	/**
	 * The rate to retrieve the room price.
	 *
	 * @var \AweBooking\Model\Pricing\Rate
	 */
	protected $room_rate;

	/**
	 * Store the breakdown of room rate.
	 *
	 * @var \AweBooking\Support\Collection
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
	 * @param \AweBooking\Availability\Request    $request   The res request.
	 * @param \AweBooking\Model\Room_Type         $room_type The room type instance.
	 * @param \AweBooking\Model\Pricing\Rate_Plan $rate_plan The rate plan instance.
	 */
	public function __construct( Request $request, Room_Type $room_type, Rate_Plan $rate_plan ) {
		$this->request = $request;
		$request->get_timespan()->requires_minimum_nights( 1 );

		$this->rooms_availability = new Availability( $room_type, abrs_check_room_states( $room_type->get_rooms(), $request->get_timespan(), $request->get_guest_counts(), Constants::STATE_AVAILABLE, $request->get_constraints() ) );
		$this->rates_availability = new Availability( $rate_plan, abrs_filter_rates( $rate_plan->get_rates(), $request->get_timespan(), $request->get_guest_counts() ) );

		$this->check_errors( $this->errors = new WP_Error );
	}

	/**
	 * Check the errors.
	 *
	 * @param \WP_Error $errors The WP_Error instance.
	 * @return void
	 */
	protected function check_errors( $errors ) {
		if ( $this->request->get_guest_counts()->get_totals() > $this->room_type->get( 'maximum_occupancy' ) ) {
			$errors->add( 'overflow_occupancy', esc_html__( 'Error: Maximum occupancy.', 'awebooking' ) );
		}

		if ( count( $this->availability->remains() ) === 0 ) {
			$errors->add( 'no_room_left', esc_html__( 'Sorry, there are no room available.', 'awebooking' ) );
		}

		if ( count( $this->rates_availability->remains() ) === 0 ) {
			$errors->add( 'no_rate_available', esc_html__( 'Sorry, there are no rate available.', 'awebooking' ) );
		}

		do_action( 'awebooking/room_rate/check_errors', $errors, $this );
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
	 * Gets the rooms availability instance.
	 *
	 * @return \AweBooking\Availability\Availability
	 */
	public function get_availability() {
		return $this->rooms_availability;
	}

	/**
	 * Gets the rates availability instance.
	 *
	 * @return \AweBooking\Availability\Availability
	 */
	public function get_rates_availability() {
		return $this->rates_availability;
	}

	/**
	 * Gets the room type instance.
	 *
	 * @return \AweBooking\Model\Room_Type
	 */
	public function get_room_type() {
		return $this->rooms_availability->get_resource();
	}

	/**
	 * Gets the rate plan instance.
	 *
	 * @return \AweBooking\Model\Pricing\Rate_Plan
	 */
	public function get_rate_plan() {
		return $this->rates_availability->get_resource();
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
	 * Gets the remain rooms.
	 *
	 * @return \AweBooking\Support\Collection
	 */
	public function get_remain_rooms() {
		return $this->rooms_availability->remains();
	}

	/**
	 * Gets the reject rooms.
	 *
	 * @return \AweBooking\Support\Collection
	 */
	public function get_reject_rooms() {
		return $this->rooms_availability->excludes();
	}

	/**
	 * Determines if room rate is read-only.
	 *
	 * @return bool
	 */
	public function is_readonly() {
		return count( $this->errors->errors ) > 0;
	}

	/**
	 * Setup the rooms availability and pricing.
	 *
	 * @return void
	 */
	public function setup() {
		if ( $this->is_readonly() ) {
			return;
		}

		$this->using( apply_filters( 'awebooking/room_rate/selected_rate', $this->rates_availability->select( 'first' ), $this->rates_availability, $this ) );

		do_action( 'awebooking/setup_room_rate', $this );

		$this->calculate_totals();
	}

	/**
	 * Sets the room rate (the room price).
	 *
	 * @param \AweBooking\Model\Pricing\Rate $rate The rate instance.
	 */
	public function using( Rate $rate ) {
		if ( ! $this->rates_availability->remain( $rate->get_id() ) ) {
			throw new \InvalidArgumentException( '' );
		}

		$this->room_rate = $rate;
		$this->breakdown = $this->retrieve_rate_breakdown( $rate );

		$this->calculate_totals();

		return $this;
	}

	/**
	 * Add a additional rate.
	 *
	 * @param  \AweBooking\Model\Pricing\Rate $rate   The rate instance.
	 * @param  string                         $reason The reason message.
	 */
	public function additional( Rate $rate, $reason = '' ) {
		$key = $rate->get_id();

		if ( is_null( $this->room_rate ) ) {
			throw new \InvalidArgumentException( 'Do it wrong' );
		}

		if ( $this->room_rate->get_id() === $key ) {
			throw new \InvalidArgumentException( 'Can not add an duplicate rate.' );
		}

		$this->additional_rates[ $key ]      = compact( 'reason', 'rate' );
		$this->additional_breakdowns[ $key ] = $this->retrieve_rate_breakdown( $rate );
		$this->calculate_totals();

		return $this;
	}

	/**
	 * Retrieve the price breakdown of given rate.
	 *
	 * @param  \AweBooking\Model\Pricing\Rate $rate The rate instance.
	 * @return \AweBooking\Support\Collection
	 *
	 * @throws \Exception
	 */
	public function retrieve_rate_breakdown( Rate $rate ) {
		$breakdown = abrs_retrieve_rate( $rate, $this->request->get_timespan() );

		if ( is_wp_error( $breakdown ) ) {
			throw new \Exception( '' );
		}

		return $breakdown;
	}

	/**
	 * Perform calculate the prices.
	 *
	 * @return void
	 */
	public function calculate_totals() {
		if ( $this->is_readonly() || empty( $this->room_rate ) ) {
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

		$this->prices = apply_filters( 'awebooking/room_rate/apply_calculated_prices', [
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
	 * @return \AweBooking\Model\Pricing\Rate|null
	 */
	public function get_room_rate() {
		return $this->room_rate;
	}

	/**
	 * Gets the rate breakdown.
	 *
	 * @return \AweBooking\Support\Collection|null
	 */
	public function get_breakdown() {
		return $this->breakdown;
	}

	/**
	 * Gets the additional breakdowns.
	 *
	 * @return array \AweBooking\Support\Collection[]
	 */
	public function get_additional_breakdowns() {
		return $this->additional_breakdowns;
	}

	/**
	 * Gets the rate (total).
	 *
	 * @return int|float
	 */
	public function get_rate() {
		return $this->prices['rate'];
	}

	/**
	 * Gets the rate.
	 *
	 * @param  string $type The price type.
	 * @return \AweBooking\Support\Decimal
	 */
	public function get_price( $type = 'rate' ) {
		return array_key_exists( $type, $this->prices )
			? abrs_decimal( $this->prices[ $type ] )
			: abrs_decimal( 0 );
	}

	/**
	 * Gets the errors.
	 *
	 * @return WP_Error
	 */
	public function get_errors() {
		return $this->errors;
	}

	/**
	 * Determines if current room rate have any errors.
	 *
	 * @param  string $code Check for specified error code.
	 * @return boolean
	 */
	public function has_error( $code = null ) {
		if ( is_null( $code ) ) {
			return count( $this->errors->errors ) > 0;
		}

		return ! empty( $this->errors->errors[ $code ] );
	}

	/**
	 * Get a single error message.
	 *
	 * @param  string $code Optional. Error code to retrieve message.
	 * @return string
	 */
	public function get_error_message( $code = null ) {
		return $this->errors->get_error_message( $code );
	}
}
