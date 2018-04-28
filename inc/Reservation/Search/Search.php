<?php
namespace AweBooking\Reservation\Search;

use WP_Query;
use AweBooking\Constants;
use AweBooking\Model\Room_Type;
use AweBooking\Model\Common\Timespan;
use AweBooking\Model\Pricing\Rate_Plan;
use AweBooking\Model\Pricing\Standard_Plan;
use AweBooking\Calendar\Finder\Finder;
use AweBooking\Calendar\Finder\State_Finder;
use AweBooking\Calendar\Resource\Resource;
use AweBooking\Calendar\Provider\Cached_Provider;
use AweBooking\Calendar\Provider\Core\State_Provider;
use AweBooking\Reservation\Request;
use AweBooking\Reservation\Constraints\MinMax_Nights_Constraint;

use AweBooking\Reservation\Room_Stay;

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
		$room_stay = new Room_Stay( $this->request, abrs_get_room_type( 83 ) );

		// First, get all room types.
		$room_types = $this->list_room_types();

		// Prepare the results.
		$results = new Results;
		$request = $this->request;

		foreach ( $room_types as $room_type ) {
			// Check the availability of rooms.
			$rooms = new Availability( $room_type, $request,
				$this->perform_find_rooms( $room_type )
			);

			// No rooms left, ignore this from the results.
			if ( count( $rooms->remains() ) == 0 ) {
				continue;
			}

			// Check the availability of rate plans.
			$plans = $this->check_rate_plans( $room_type );

			// Push to the results.
			$results->push( compact( 'request', 'room_type', 'rooms', 'plans' ) );
		}

		return $results;
	}

	/**
	 * Perform find the rooms available.
	 *
	 * @param  \AweBooking\Model\Room_Type $room_type The room type.
	 * @return \AweBooking\Calendar\Finder\Response
	 */
	protected function perform_find_rooms( $room_type ) {
		// Requires at least 1 night.
		$timespan = $this->request->get_timespan();
		$timespan->requires_minimum_nights( 1 );

		// Transform the rooms into resources.
		$resources = $room_type->get_rooms()
			->map( function( $room_unit ) use ( $room_type ) {
				$resource = new Resource( $room_unit->get_id(), Constants::STATE_AVAILABLE );

				$resource->set_reference( $room_unit );
				$resource->with_constraints( apply_filters( 'awebooking/reservation/room_constraints', [], $room_unit, $room_type ) );

				return $resource;
			});

		// Create the provider.
		$provider = abrs_calendar_provider( 'state', $resources, true );
		$provider = apply_filters( 'awebooking/reservation/room_state_provider', $provider, $resources );

		return ( new State_Finder( $resources, $provider ) )
			->only( [ Constants::STATE_AVAILABLE ] )
			->using( $this->constraints )
			->find( $timespan->to_period( Constants::GL_NIGHTLY ) );
	}

	/**
	 * Perform check the available of rate_plans.
	 *
	 * @param  \AweBooking\Model\Room_Type $room_type The room type.
	 * @return \AweBooking\Support\Collection
	 */
	protected function check_rate_plans( $room_type ) {
		$rate_plans = $room_type
			->get_rate_plans()
			->filter( $this->filter_rate_plans() )
			->reject( function ( $plan ) {
				return count( $plan->get_rates() ) === 0;
			})->values();

		// Prepare response.
		$plans = abrs_collect();

		foreach ( $rate_plans as $plan ) {
			$rates = new Availability( $plan, $this->request,
				$this->perform_find_rates( $plan )
			);

			$plans->put( $plan->get_id(), compact( 'plan', 'rates' ) );
		}

		return $plans;
	}

	/**
	 * Filter rate_plans by request.
	 *
	 * @return Clousure
	 */
	protected function filter_rate_plans() {
		return function( $plan ) {
			if ( empty( $this->request['rate_plans'] ) ) {
				return $plan;
			}

			// Special filter for 'standard' plan.
			if ( 'standard' === $this->request['rate_plans'] ) {
				return $plan instanceof Standard_Plan;
			}

			return in_array( $plan->get_id(), wp_parse_id_list( $this->request['rate_plans'] ) );
		};
	}

	/**
	 * Perform filter the rate plans.
	 *
	 * @param  \AweBooking\Model\Pricing\Rate_Plan $rate_plan The room type.
	 * @return \AweBooking\Calendar\Finder\Response
	 */
	protected function perform_find_rates( $rate_plan ) {
		$timespan = $this->request->get_timespan();

		// Transform the rate plans into resources.
		$resources = $rate_plan->get_rates()
			->map( function( $rate ) use ( $rate_plan ) {
				$resource = new Resource( $rate->get_id() );

				$resource->set_reference( $rate );
				$resource->with_constraints( $this->get_rate_constraints( $rate, $rate_plan ) );

				return $resource;
			});

		return ( new Finder( $resources ) )
			// ->using( $this->constraints )
			->find( $timespan->to_period( Constants::GL_NIGHTLY ) );
	}

	/**
	 * Returns the rate constraints based on a rate.
	 *
	 * @param  \AweBooking\Model\Pricing\Rate      $rate      The rate instance.
	 * @param  \AweBooking\Model\Pricing\Rate_Plan $rate_plan The rate plan instance.
	 * @return array
	 */
	protected function get_rate_constraints( $rate, $rate_plan ) {
		$restrictions = $rate->get_restrictions();

		$constraints = [];
		if ( $restrictions['min_los'] || $restrictions['max_los'] ) {
			$constraints[] = new MinMax_Nights_Constraint( $this->request, $rate->get_id(), $restrictions['min_los'], $restrictions['max_los'] );
		}

		return apply_filters( 'awebooking/reservation/rate_plans_constraints', $constraints, $rate_plan );
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

		// Filter get only room types.
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
