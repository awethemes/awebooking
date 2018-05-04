<?php
namespace AweBooking\Reservation\Room_Stay;

use WP_Error;
use AweBooking\Constants;
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
	 * The booked rate-plan.
	 *
	 * @var \AweBooking\Model\Pricing\Rate_Plan
	 */
	protected $rate_plan;

	/**
	 * The reservation request.
	 *
	 * @var \AweBooking\Reservation\Request
	 */
	protected $request;

	/**
	 * The filtered rates.
	 *
	 * @var \AweBooking\Reservation\Room_Stay\Availability
	 */
	protected $filtered_rates;

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
	 * The assigned room to stay.
	 *
	 * @var \AweBooking\Model\Room
	 */
	protected $assigned_room;

	protected $pricing = [
		'price'             => 0,
		'price_per_night'   => 0,
		'price_first_night' => 0,
		'total'             => 0,
	];

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
		if ( ! $this->request ) {
			$this->request = new Request( $this->timespan, $this->guest_counts, [] );
		}

		return $this->request;
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
	 * Sets the room constraints.
	 *
	 * @param array $constraints Array of constraints.
	 */
	public function set_room_constraints( $constraints = [] ) {
		$this->room_constraints = $constraints;
	}

	/**
	 * Sets the room availability.
	 *
	 * @param \AweBooking\Reservation\Room_Stay\Availability $availability The availability instance.
	 */
	public function set_availability( Availability $availability ) {
		$this->availability = $availability;
	}

	/**
	 * Get the availability of rooms in room type.
	 *
	 * @return \AweBooking\Reservation\Room_Stay\Availability
	 */
	public function get_availability() {
		if ( is_null( $this->availability ) ) {
			$this->availability = new Availability( $this->room_type,
				abrs_check_room_state( $this->room_type->get_rooms(), $this->timespan, Constants::STATE_AVAILABLE, $this->room_constraints )
			);
		}

		return $this->availability;
	}

	/**
	 * Gets the total costs.
	 *
	 * @return \AweBooking\Support\Decimal
	 */
	public function get_total() {
		$this->calculate_costs();

		return $this->pricing['price'];
	}

	/**
	 * Perform calculate costs.
	 *
	 * @return void
	 */
	public function calculate_costs() {
		$rate_plan = $this->get_rate_plan();

		if ( is_null( $this->filtered_rates ) ) {
			$this->filtered_rates = new Availability( $rate_plan, abrs_filter_rates( $rate_plan->get_rates(), $this->get_request() ) );
		}

		if ( 0 === count( $this->filtered_rates->remains() ) ) {
			$this->errors->add( 'no_rate_found', esc_html__( 'No rate found.', 'awebooking' ) );
			return;
		}

		// Select the room rate.
		$this->room_rate = $this->filtered_rates->select( 'last' );
		$room_breakdown = abrs_retrieve_rate( $this->room_rate, $this->get_timespan() );

		$this->pricing['price'] = $room_breakdown->sum();
		$this->pricing['price_per_night'] = abrs_decimal( $room_breakdown->avg() )->as_numeric();
		$this->pricing['price_first_night'] = $room_breakdown->first();

		// Calculate all mandatory services.
		// ...
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
