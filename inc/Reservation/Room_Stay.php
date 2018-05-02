<?php
namespace AweBooking\Reservation;

use AweBooking\Constants;
use AweBooking\Model\Room;
use AweBooking\Model\Room_Type;
use AweBooking\Model\Common\Timespan;
use AweBooking\Model\Common\Guest_Counts;
use AweBooking\Model\Pricing\Rate_Plan;
use AweBooking\Support\Traits\Fluent_Getter;

use AweBooking\Finder\Rate_Finder;
use AweBooking\Calendar\Resource\Resource;
use AweBooking\Reservation\Constraints\Night_Stay_Constraint;

class Room_Stay {
	use Fluent_Getter;

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
	 * The room assigned to stay.
	 *
	 * @var \AweBooking\Model\Room
	 */
	protected $assigned_room;

	protected $using_rate;

	protected $addination_rates;

	/**
	 * [__construct description]
	 *
	 * @param Timespan       $timespan     [description]
	 * @param Guest_Counts   $guest_counts [description]
	 * @param Room_Type      $room_type    [description]
	 * @param Rate_Plan|null $rate_plan    [description]
	 */
	public function __construct( Timespan $timespan, Guest_Counts $guest_counts, Room_Type $room_type, Rate_Plan $rate_plan = null ) {
		$this->timespan     = $timespan;
		$this->guest_counts = $guest_counts;
		$this->room_type    = $room_type;
		$this->rate_plan    = $rate_plan ?: $room_type->get_standard_plan();
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
	 * [get_rates description]
	 *
	 * @return [type]
	 */
	public function get_rates() {
		return $this->rate_plan->get_rates();
	}


	/**
	 * Gets the total costs.
	 *
	 * @return \AweBooking\Support\Decimal
	 */
	public function get_total() {
		$this->calculate_costs();
	}

	/**
	 * Perform calculate the costs.
	 *
	 * @return array
	 */
	public function calculate_costs() {
		if ( is_null( $this->using_rate ) ) {
			$this->using_rate = $this->filter_rate()->first();
		}

		$rates = abrs_retrieve_rate( $rate, $this->get_timespan() );

		dd( $rates );
	}

	/**
	 * Perform find rates.
	 *
	 * @param  array $rates The list of rates.
	 * @return \AweBooking\Finder\Response
	 */
	protected function filter_rate() {
		$resources = abrs_collect( $this->get_rates() )->map( function( $rate ) {
			$resource = new Resource( $rate->get_id() );

			$resource->set_reference( $rate );
			$resource->with_constraints( $this->get_rate_constraints( $rate ) );

			return $resource;
		});

		return ( new Rate_Finder( $resources ) )
			// ->using( $this->constraints )
			->find( $this->timespan->to_period( Constants::GL_NIGHTLY ) );
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
			$constraints[] = new Night_Stay_Constraint( $this->request, $rate->get_id(), $restrictions['min_los'], $restrictions['max_los'] );
		}

		return apply_filters( 'awebooking/reservation/rate_constraints', $constraints );
	}
}
