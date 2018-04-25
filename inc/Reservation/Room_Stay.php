<?php
namespace AweBooking\Reservation;

use AweBooking\Constants;
use AweBooking\Model\Room;
use AweBooking\Model\Room_Type;
use AweBooking\Model\Pricing\Rate_Plan;
use AweBooking\Support\Traits\Fluent_Getter;

use AweBooking\Calendar\Finder\Finder;
use AweBooking\Calendar\Resource\Resource;
use AweBooking\Reservation\Constraints\MinMax_Nights_Constraint;

class Room_Stay {
	use Fluent_Getter;

	/**
	 * The request instance.
	 *
	 * @var \AweBooking\Reservation\Request
	 */
	protected $request;

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
	 * The room assigned to stay.
	 *
	 * @var \AweBooking\Model\Room
	 */
	protected $assigned_room;

	/**
	 * Create new room-stay.
	 *
	 * @param \AweBooking\Reservation\Request     $request   The reservation request.
	 * @param \AweBooking\Model\Room_Type         $room_type The room_type.
	 * @param \AweBooking\Model\Pricing\Rate_Plan $rate_plan The rate_plan.
	 */
	public function __construct( Request $request, Room_Type $room_type, Rate_Plan $rate_plan = null ) {
		$this->request   = $request;
		$this->room_type = $room_type;
		$this->rate_plan = ! is_null( $rate_plan ) ? $rate_plan : $room_type->get_standard_plan();
	}

	/**
	 * Assign a room into the room stay.
	 *
	 * @param  \AweBooking\Model\Room $room The room.
	 * @return $this
	 */
	public function assign( Room $room ) {
		$this->assigned_room = $room;

		return $this;
	}

	/**
	 * Get the room-type.
	 *
	 * @return \AweBooking\Model\Room_Type
	 */
	public function get_room_type() {
		return $this->room_type;
	}

	/**
	 * Get the rate_plan.
	 *
	 * @return \AweBooking\Model\Pricing\Rate_Plan
	 */
	public function get_rate_plan() {
		return $this->rate_plan;
	}

	/**
	 * Get the assigned_room.
	 *
	 * @return \AweBooking\Model\Room|null
	 */
	public function get_assigned_room() {
		return $this->assigned_room;
	}

	/**
	 * Determines if current room stay is bookable.
	 *
	 * @return boolean
	 */
	public function is_bookable() {
		return false;
	}

	/**
	 * Perform calculate the price of room based on the request.
	 *
	 * @return array
	 */
	public function calculate_price() {
		$applied_rates = [];

		$timespan  = $this->request->get_timespan();
		$room_type = $this->room_type;

		// First we need get all rates of selected rate plan,
		// then, perform find one base rate.
		$all_rates = $this->rate_plan->get_rates();

		// Calculator rate price.
		$response = $this->perform_find_rates( $all_rates );

		// Begin at zero.
		$total = abrs_decimal( 0 );

		if ( count( $response->get_included() ) > 0 ) {
			$rate = $response->get_included()->first()['resource']->get_reference();

			list( $rate_price, $rate_breakdown ) = abrs_retrieve_price( $rate, $timespan );

			$total = $total->add( $rate_price );
		}

		// Potentially increase costs if dealing with persons.
		// TODO: ...

		// Calculate all mandatory services cost.
		// $room_services = $room_type->get_services();

		return $total;
	}

	/**
	 * Perform find rates.
	 *
	 * @param  array $rates The list of rates.
	 * @return \AweBooking\Calendar\Finder\Response
	 */
	protected function perform_find_rates( $rates ) {
		$timespan = $this->request->get_timespan();

		$resources = abrs_collect( $rates )->map( function( $rate ) {
			$resource = new Resource( $rate->get_id() );

			$resource->set_reference( $rate );
			$resource->with_constraints( $this->get_rate_constraints( $rate ) );

			return $resource;
		});

		return ( new Finder( $resources ) )
			// ->using( $this->constraints )
			->find( $timespan->to_period( Constants::GL_NIGHTLY ) );
	}

	/**
	 * Returns the rate constraints based on a rate.
	 *
	 * @param  \AweBooking\Model\Pricing\Rate $rate The rate instance.
	 * @return array
	 */
	protected function get_rate_constraints( $rate ) {
		$restrictions = $rate->get_restrictions();

		$constraints = [];
		if ( $restrictions['min_los'] || $restrictions['max_los'] ) {
			$constraints[] = new MinMax_Nights_Constraint( $this->request, $rate->get_id(), $restrictions['min_los'], $restrictions['max_los'] );
		}

		return apply_filters( 'awebooking/reservation/rate_constraints', $constraints );
	}
}
