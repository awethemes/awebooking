<?php
namespace AweBooking\Reservation\Room_Stay;

use WP_Error;
use AweBooking\Constants;
use AweBooking\Model\Room_Type;
use AweBooking\Model\Pricing\Rate;
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
	 * The booked rate-plan.
	 *
	 * @var \AweBooking\Model\Pricing\Rate_Plan
	 */
	protected $rate_plan;

	/**
	 * The logging errors.
	 *
	 * @var WP_Error
	 */
	protected $errors;

	/**
	 * The constraints apply to room availability.
	 *
	 * @var array
	 */
	protected $constraints = [];

	/**
	 * The room availability.
	 *
	 * @var \AweBooking\Reservation\Room_Stay\Availability
	 */
	protected $availability;

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
	 * The filtered rates.
	 *
	 * @var \AweBooking\Reservation\Room_Stay\Availability
	 */
	protected $filtered_rates;

	/**
	 * Store the calculated prices.
	 *
	 * @var array
	 */
	public $prices = [
		'rate'             => 0,
		'rate_per_night'   => 0,
		'rate_first_night' => 0,
		'additionals'      => 0,
		'total'            => 0,
	];

	/**
	 * Store the breakdowns of rates.
	 *
	 * @var array
	 */
	public $breakdowns = [
		'room_rate'   => null,
		'additionals' => [],
	];

	/**
	 * Did the room rate has been setup or not.
	 *
	 * @var boolean
	 */
	protected $did_setup = false;

	/**
	 * Constructor.
	 *
	 * @param Timespan       $timespan     The timespan.
	 * @param Guest_Counts   $guest_counts The guest counts.
	 * @param Room_Type      $room_type    The room type.
	 * @param Rate_Plan|null $rate_plan    The rate plan.
	 */
	public function __construct( Timespan $timespan, Guest_Counts $guest_counts, Room_Type $room_type, Rate_Plan $rate_plan ) {
		$this->timespan     = $timespan;
		$this->guest_counts = $guest_counts;
		$this->room_type    = $room_type;
		$this->rate_plan    = $rate_plan;
		$this->errors       = new WP_Error;
	}

	/**
	 * Sets the room constraints.
	 *
	 * @param array $constraints Array of constraints.
	 */
	public function set_constraints( $constraints = [] ) {
		$this->constraints = $constraints;
	}

	/**
	 * Sets the room rate (the room price).
	 *
	 * @param \AweBooking\Model\Pricing\Rate $rate The rate instance.
	 */
	public function set_room_rate( Rate $rate ) {
		$this->room_rate = $rate;
	}

	/**
	 * Additional a rate.
	 *
	 * @param  \AweBooking\Model\Pricing\Rate $rate  The rate instance.
	 * @param  string                         $title The title.
	 */
	public function additional_rate( Rate $rate, $title = '' ) {
		$this->additional_rates[ $rate->get_id() ] = compact( 'title', 'rate' );
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

		if ( ! $this->has_error() ) {
			$this->availability = new Availability( $this->room_type,
				abrs_check_room_states( $this->room_type->get_rooms(), $this->timespan, $this->guest_counts, Constants::STATE_AVAILABLE, $this->constraints )
			);

			if ( count( $this->availability->remains() ) === 0 ) {
				$this->errors->add( 'no_room_left', esc_html__( 'No room available.', 'awebooking' ) );
			}

			$this->filtered_rates = new Availability( $this->rate_plan,
				abrs_filter_rates( $this->rate_plan->get_rates(), $this->timespan, $this->guest_counts )
			);

			if ( count( $this->filtered_rates->remains() ) === 0 ) {
				$this->errors->add( 'no_rate_available', esc_html__( 'No room rate available.', 'awebooking' ) );
			} else {
				$this->set_room_rate( $this->filtered_rates->select( 'last' ) );
			}

			do_action( 'awebooking/setup_room_rate', $this );

			$this->calculate_costs();
		}

		$this->did_setup = true;
	}

	/**
	 * Pre-check for the timespan, occupancy, etc.
	 *
	 * @return void
	 */
	protected function precheck() {
		if ( $this->guest_counts->get_totals() > $this->room_type->get( 'maximum_occupancy' ) ) {
			$this->errors->add( 'overflow_occupancy', esc_html__( 'Maximum occupancy', 'awebooking' ) );
		}

		do_action( 'awebooking/precheck_room_rate', $this );
	}

	/**
	 * Perform setup the prices.
	 *
	 * @return void
	 */
	protected function calculate_costs() {
		if ( $this->has_error() || empty( $this->room_rate ) ) {
			return;
		}

		// Retrieve room rate breakdown.
		$breakdown = abrs_retrieve_rate( $this->room_rate, $this->timespan );

		if ( is_wp_error( $breakdown ) ) {
			$this->errors->add( 'rate_error', esc_html__( 'Can\'t find the room rate.', 'awebooking' ) );
			return;
		}

		if ( ( $rate = $breakdown->sum() ) <= 0 ) {
			$this->errors->add( 'zero_rate', esc_html__( 'Invalid room rate.', 'awebooking' ) );
			return;
		}

		$total = $rate;
		$this->breakdowns['room_rate'] = $breakdown;

		// Calculate additional_rates.
		foreach ( $this->additional_rates as $_rate ) {
			$_breakdown = abrs_retrieve_rate( $_rate, $this->timespan );

			if ( is_wp_error( $_breakdown ) ) {
				continue;
			}

			$total += $_breakdown->sum();
			$this->breakdowns['additionals'][ $_rate->get_id() ] = $_breakdown;
		}

		// Sets the prices.
		$this->prices = apply_filters( 'awebooking/setup_rate_prices', [
			'total'            => $total,
			'rate'             => $rate,
			'additionals'      => $total - $rate,
			'rate_per_night'   => $breakdown->avg(),
			'rate_first_night' => $breakdown->first(),
		], $this );

		do_action( 'awebooking/calculate_room_rate', $this );
	}

	/**
	 * Check for call the setup method.
	 *
	 * @return void
	 */
	protected function check_setup() {
		if ( ! $this->did_setup ) {
			$this->setup();
		}
	}

	/**
	 * Determines if current room rate is bookable.
	 *
	 * @return boolean
	 */
	public function is_bookable() {
		$this->check_setup();

		if ( $this->has_error() || $this->get_price( 'total' )->is_zero() ) {
			return false;
		}

		return apply_filters( 'awebooking/is_bookable', true, $this );
	}

	/**
	 * Get the availability of rooms in room type.
	 *
	 * @return \AweBooking\Reservation\Room_Stay\Availability
	 */
	public function get_availability() {
		$this->check_setup();

		return $this->availability;
	}

	/**
	 * Gets the rooms still remain.
	 *
	 * @return \AweBooking\Support\Collection
	 */
	public function get_remain_rooms() {
		return $this->get_availability()->remains();
	}

	/**
	 * Get the assigned_room.
	 *
	 * @return \AweBooking\Model\Room|null
	 */
	public function get_assigned_room() {
		return $this->get_availability()->select( 'first' );
	}

	/**
	 * Gets the rate.
	 *
	 * @param  string $type The price type.
	 * @return \AweBooking\Support\Decimal
	 */
	public function get_price( $type = 'total' ) {
		$this->check_setup();

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
	 * @param  string $code Check for special error code.
	 * @return boolean
	 */
	public function has_error( $code = null ) {
		return is_null( $code )
			? ! empty( $this->errors->errors )
			: ! empty( $this->errors->errors[ $code ] );
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
