<?php
namespace AweBooking\Reservation\Room_Stay;

use WP_Error;
use AweBooking\Constants;
use AweBooking\Model\Room;
use AweBooking\Model\Room_Type;
use AweBooking\Model\Pricing\Rate_Plan;
use AweBooking\Model\Common\Timespan;
use AweBooking\Model\Common\Guest_Counts;
use AweBooking\Reservation\Request;

class Room_Rate {
	/**
	 * The Timespan instance.
	 *
	 * @var \AweBooking\Model\Common\Timespan
	 */
	protected $timespan;

	/**
	 * The Guest_Counts instance.
	 *
	 * @var \AweBooking\Model\Common\Guest_Counts|null
	 */
	protected $guest_counts;

	/**
	 * The booked room-type.
	 *
	 * @var \AweBooking\Model\Room_Type
	 */
	protected $room_type;

	/**
	 * The assigned room to stay.
	 *
	 * @var \AweBooking\Model\Room
	 */
	protected $assigned_room;

	/**
	 * The booked rate-plan.
	 *
	 * @var \AweBooking\Model\Pricing\Rate_Plan
	 */
	protected $rate_plan;

	/**
	 * The rate to retrieve the room price.
	 *
	 * @var \AweBooking\Model\Pricing\Rate
	 */
	protected $room_rate;

	/**
	 * The additional rates add to to room price.
	 *
	 * @var array
	 */
	protected $additional_rates = [];

	/**
	 * The constraints apply to room availability.
	 *
	 * @var array
	 */
	protected $room_constraints = [];

	/**
	 * The room availability.
	 *
	 * @var \AweBooking\Reservation\Room_Stay\Availability
	 */
	protected $availability;

	/**
	 * The filtered rates.
	 *
	 * @var \AweBooking\Reservation\Room_Stay\Availability
	 */
	protected $filtered_rates;

	/**
	 * [$prices description]
	 *
	 * @var [type]
	 */
	protected $prices = [
		'room_rate' => 0,
	];

	/**
	 * Did room rate has been setup or not.
	 *
	 * @var boolean
	 */
	protected $did_setup = false;

	/**
	 * The logging errors.
	 *
	 * @var WP_Error
	 */
	protected $errors;

	/**
	 * Constructor.
	 *
	 * @param Timespan       $timespan     The timespan.
	 * @param Guest_Counts   $guest_counts The guest counts.
	 * @param Room_Type      $room_type    The room type.
	 * @param Rate_Plan|null $rate_plan    The rate plan.
	 */
	public function __construct( Timespan $timespan, Guest_Counts $guest_counts, Room_Type $room_type, Rate_Plan $rate_plan = null ) {
		$this->timespan     = $timespan;
		$this->guest_counts = $guest_counts;
		$this->room_type    = $room_type;
		$this->rate_plan    = $rate_plan ?: $room_type->get_standard_plan();
		$this->errors       = new WP_Error;
	}

	/**
	 * Sets the room constraints.
	 *
	 * @param array $constraints Array of constraints.
	 */
	public function set_room_constraints( $constraints = [] ) {
		$this->room_constraints = $constraints;
	}

	/**
	 * Setup the rooms availability and pricing.
	 *
	 * @return void
	 */
	public function setup() {
		if ( $this->did_setup ) {
			return;
		}

		$this->precheck();

		if ( ! $this->is_error() ) {
			$this->availability = new Availability( $this->room_type,
				abrs_check_rooms( $this->room_type->get_rooms(), $this->timespan, $this->guest_counts, Constants::STATE_AVAILABLE, $this->room_constraints )
			);

			if ( count( $this->availability->remains() ) === 0 ) {
				$this->errors->add( 'room_error', esc_html__( 'No room available.', 'awebooking' ) );
				return;
			}

			$this->filtered_rates = new Availability( $this->rate_plan,
				abrs_filter_rates( $this->rate_plan->get_rates(), $this->timespan, $this->guest_counts )
			);

			if ( count( $this->filtered_rates->remains() ) === 0 ) {
				$this->errors->add( 'rate_error', esc_html__( 'No room rate available.', 'awebooking' ) );
				return;
			}
		}

		do_action( 'awebooking/setup_room_rate', $this );

		$this->did_setup = true;
	}

	/**
	 * Pre-check for the timespan, occupancy, etc.
	 *
	 * @return void
	 */
	protected function precheck() {
		if ( $this->guest_counts->get_totals() > $this->room_type->get( 'maximum_occupancy' ) ) {
			$this->errors->add( 'occupancy_error', esc_html__( 'Maximum occupancy', 'awebooking' ) );
		}

		do_action( 'awebooking/precheck_room_rate', $this );
	}

	/**
	 * Perform setup the prices.
	 *
	 * @return void
	 */
	public function calculate_costs() {
		// Leave if we got any rate_error.
		if ( $this->is_error( 'rate_error' ) ) {
			return;
		}

		// Select the room rate.
		if ( is_null( $this->room_rate ) ) {
			$this->room_rate = $this->filtered_rates->select( 'last' );
		}

		$breakdown = abrs_retrieve_rate( $this->room_rate, $this->get_timespan() );
		if ( is_wp_error( $breakdown ) ) {
			$this->errors->add( 'rate_error', esc_html__( 'Invalid room rate.', 'awebooking' ) );
			return;
		}

		$room_rate = $breakdown->sum();
		$total     = $room_rate;

		if ( $room_rate <= 0 ) {
			$this->errors->add( 'rate_error', esc_html__( 'Invalid room rate amount.', 'awebooking' ) );
			return;
		}

		// Calculate additional_rates.
		foreach ( $this->additional_rates as $_rate ) {
			$_breakdown = abrs_retrieve_rate( $_rate, $this->get_timespan() );
			if ( is_wp_error( $breakdown ) ) {
				continue;
			}

			$total += $_breakdown->sum();
		}

		// Sets the prices.
		$this->prices = apply_filters( 'awebooking/room_rate/prices', [
			'total'            => 0,
			'room_rate'        => $room_rate,
			'rate_per_night'   => $breakdown->avg(),
			'rate_first_night' => $breakdown->first(),
		], $this );

		do_action( 'awebooking/room_rate/calculate_costs', $this );
	}

	/**
	 * Determines if current room rate is bookable.
	 *
	 * @return boolean
	 */
	public function is_bookable() {
		if ( $this->is_error() || $this->get_price( 'room_rate' ) <= 0 ) {
			return false;
		}

		return apply_filters( 'awebooking/room_rate/is_bookable', true, $this );
	}

	/**
	 * Get the availability of rooms in room type.
	 *
	 * @return \AweBooking\Reservation\Room_Stay\Availability
	 */
	public function get_availability() {
		return $this->availability;
	}

	/**
	 * [get_remain_rooms description]
	 *
	 * @return [type]
	 */
	public function get_remain_rooms() {
		return $this->availability->remains();
	}

	/**
	 * Gets the number of nights.
	 *
	 * @return int
	 */
	public function get_nights_stay() {
		return $this->timespan->get_nights();
	}

	/**
	 * Gets the total costs.
	 *
	 * @return \AweBooking\Support\Decimal
	 */
	public function get_price( $type = 'room_rate' ) {
		$this->calculate_costs();

		switch ( $type ) {
			case 'first_night':
				return $this->prices['price_first_night'];
			case 'per_ngiht':
				return $this->prices['price_per_night'];
			default:
				return $this->prices['room_rate'];
		}
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
	 * @param  string $code Check for special error code.
	 * @return boolean
	 */
	public function is_error( $code = null ) {
		return is_null( $code )
			? ! empty( $this->errors->errors )
			: ! empty( $this->errors->errors[ $code ] );
	}

	/**
	 * Gets the first error message.
	 *
	 * @return string
	 */
	public function get_error_message() {
		return $this->is_error() ? $this->errors->get_error_message() : '';
	}

	/**
	 * Assign a room into the room stay.
	 *
	 * @param  \AweBooking\Model\Room $room The room.
	 * @return $this
	 */
	public function assign( Room $room ) {
		if ( (int) $room->get( 'room_type' ) !== $this->room_type->get_id() ) {
			return false;
		}

		$this->assigned_room = $room;

		return true;
	}

	/**
	 * Get the assigned_room.
	 *
	 * @return \AweBooking\Model\Room|null
	 */
	public function get_assigned() {
		return $this->assigned_room;
	}


	/**
	 * Gets the Timespan.
	 *
	 * @return \AweBooking\Model\Common\Timespan
	 */
	public function get_timespan() {
		return $this->timespan;
	}

	/**
	 * Gets the guest_counts.
	 *
	 * @return \AweBooking\Model\Common\Guest_Counts
	 */
	public function get_guest_counts() {
		return $this->guest_counts;
	}

	/**
	 * Gets the room_type instance.
	 *
	 * @return \AweBooking\Model\Room_Type
	 */
	public function get_room_type() {
		return $this->room_type;
	}

	/**
	 * Gets the rate_plan instance.
	 *
	 * @return \AweBooking\Model\Pricing\Rate_Plan
	 */
	public function get_rate_plan() {
		return $this->rate_plan;
	}

	/**
	 * Gets the reservation request.
	 *
	 * @return \AweBooking\Reservation\Request
	 */
	public function get_request() {
		return new Request( $this->timespan, $this->guest_counts );
	}

	/**
	 * Sets the current reservation request.
	 *
	 * @param  \AweBooking\Reservation\Request $request The reservation request.
	 * @return $this
	 */
	public function set_request( Request $request ) {
		$this->request = $request;

		return $this;
	}

	/**
	 * Getter protected property.
	 *
	 * @param  string $property The property name.
	 * @return mixed
	 */
	public function __get( $property ) {
		return $this->{$property};
	}

	/**
	 * Check exists a protected property.
	 *
	 * @param  string $property The property name.
	 * @return mixed
	 */
	public function __isset( $property ) {
		return isset( $this->{$property} );
	}
}
