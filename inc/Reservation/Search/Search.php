<?php
namespace AweBooking\Reservation\Search;

use WP_Query;
use AweBooking\Constants;
use AweBooking\Model\Room_Type;
use AweBooking\Model\Pricing\Rate_Plan;
use AweBooking\Reservation\Request;

use AweBooking\Model\Room;
use AweBooking\Model\Common\Timespan;

use AweBooking\Calendar\Finder\Finder;
use AweBooking\Calendar\Finder\State_Finder;
use AweBooking\Calendar\Resource\Resource;
use AweBooking\Calendar\Provider\Core\State_Provider;
use AweBooking\Calendar\Provider\Cached_Provider;
use AweBooking\Reservation\Constraints\MinMax_Nights_Constraint;

class Search {
	/**
	 * The request instance.
	 *
	 * @var \AweBooking\Reservation\Request
	 */
	protected $request;

	/**
	 * The constraints to apply to search.
	 *
	 * @var array
	 */
	protected $constraints = [];

	/**
	 * Constructor.
	 *
	 * @param \AweBooking\Reservation\Request $request     The request.
	 * @param array                           $constraints The constraints.
	 */
	public function __construct( Request $request, $constraints = [] ) {
		$this->request = $request;
		$this->constraints = $constraints;
	}

	/**
	 * Set the reservation request.
	 *
	 * @param  \AweBooking\Reservation\Request $request The request.
	 * @return $this
	 */
	public function set_request( Request $request ) {
		$this->request = $request;

		return $this;
	}

	/**
	 * Apply constraints.
	 *
	 * @param  array $constraints \AweBooking\Reservation\Search\Constraint[].
	 * @return $this
	 */
	public function set_constraints( $constraints ) {
		$this->constraints = $constraints;

		return $this;
	}

	/**
	 * Gets the search results.
	 *
	 * @return \AweBooking\Reservation\Search\Results
	 */
	public function get() {
		// First, get all room types.
		$room_types = $this->list_room_types();

		// Prepare the results.
		$results = new Results;

		foreach ( $room_types as $room_type ) {
			// Create the availability for each room type.
			$availability = new Availability( $this->request, $room_type,
				$this->perform_filter_rooms( $room_type ),
				$this->perform_filter_plans( $room_type )
			);

			// No rooms left, ignore this from the results.
			if ( count( $availability->remain_rooms() ) == 0 ) {
				continue;
			}

			$results->push( $availability );
		}

		return $results;
	}

	/**
	 * Perform filter the rooms available.
	 *
	 * @param  \AweBooking\Model\Room_Type $room_type The room type.
	 * @return \AweBooking\Calendar\Finder\Response
	 */
	protected function perform_filter_rooms( Room_Type $room_type ) {
		// Requires at least 1 night.
		$timespan = $this->request->get_timespan();
		$timespan->requires_minimum_nights( 1 );

		// Transform the rooms into resources.
		$resources = $room_type->get_rooms()->map( function( $room_unit ) use ( $room_type ) {
			$resource = new Resource( $room_unit->get_id(), Constants::STATE_AVAILABLE );

			$resource->set_reference( $room_unit );
			$resource->with_constraints( apply_filters( 'awebooking/reservation/room_constraints', [], $room_unit, $room_type ) );

			return $resource;
		});

		// Create the provider.
		$provider = new Cached_Provider( new State_Provider( $resources ) );
		$provider = apply_filters( 'awebooking/reservation/room_state_provider', $provider, $resources );

		return ( new State_Finder( $resources, $provider ) )
			->only( [ Constants::STATE_AVAILABLE ] )
			->using( $this->constraints )
			->find( $timespan->to_period( Constants::GL_NIGHTLY ) );
	}

	/**
	 * Perform filter the rate plans.
	 *
	 * @param  \AweBooking\Model\Room_Type $room_type The room type.
	 * @return \AweBooking\Calendar\Finder\Response
	 */
	protected function perform_filter_plans( Room_Type $room_type ) {
		// Requires at least 1 night.
		$timespan = $this->request->get_timespan();
		$timespan->requires_minimum_nights( 1 );

		// Transform the rate plans into resources.
		$resources = $room_type->get_rate_plans()->map( function( $rate_plan ) use ( $room_type ) {
			$resource = new Resource( $rate_plan->get_id() );

			$resource->set_reference( $rate_plan );
			$resource->with_constraints( $this->get_rate_plans_constraints( $rate_plan, $room_type ) );

			return $resource;
		});

		return ( new Finder( $resources ) )
			->using( $this->constraints )
			->find( $timespan->to_period( Constants::GL_NIGHTLY ) );
	}

	/**
	 * [get_rate_plans_constraints description]
	 *
	 * @param  Rate_Plan $rate_plan [description]
	 * @param  \AweBooking\Model\Room_Type $room_type The room type.
	 * @return array
	 */
	protected function get_rate_plans_constraints( Rate_Plan $rate_plan, Room_Type $room_type ) {
		$restrictions = $rate_plan->get_restrictions();

		$constraints = [
			new MinMax_Nights_Constraint( $this->request, $rate_plan->get_id(), $restrictions['min_los'], $restrictions['max_los'] ),
		];

		return apply_filters( 'awebooking/reservation/rate_plans_constraints', $constraints, $rate_plan, $room_type );
	}

	/**
	 * Query the room-types matches for searching.
	 *
	 * @return \AweBooking\Support\Collection
	 */
	protected function list_room_types() {
		$guestcounts = $this->request->get_guest_counts();

		// Build the query args.
		$wp_query_args = [
			'post_type'        => Constants::ROOM_TYPE,
			'post_status'      => 'publish',
			'no_found_rows'    => true,
			'posts_per_page'   => 250, // Limit 250 items.
			'booking_adults'   => $guestcounts ? $guestcounts->get_adults()->get_count() : -1,
			'booking_children' => ( $guestcounts && $guestcounts->get_children() ) ? $guestcounts->get_children()->get_count() : -1,
			'booking_infants'  => ( $guestcounts && $guestcounts->get_infants() ) ? $guestcounts->get_infants()->get_count() : -1,
		];

		// Get only room type if requested.
		if ( ! empty( $this->request['only'] ) ) {
			$wp_query_args['post__in'] = wp_parse_id_list( $this->request['only'] );
		}

		// Perform query room types.
		$room_types = new WP_Query(
			apply_filters( 'awebooking/reservation/query_room_types', $wp_query_args, $this )
		);

		return abrs_collect( $room_types->posts )
			->map_into( Room_Type::class )
			->reject( function ( $rt ) {
				return count( $rt->get_rooms() ) === 0;
			})->values();
	}
}
