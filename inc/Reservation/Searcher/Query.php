<?php
namespace AweBooking\Reservation\Searcher;

use WP_Query;
use AweBooking\Constants;
use AweBooking\Model\Room_Type;
use AweBooking\Reservation\Request;

class Query {
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
	 * Get the availability results.
	 *
	 * @return \AweBooking\Reservation\Searcher\Results
	 */
	public function get() {
		$results = new Results( $this->request );
		$checker = new Checker;

		$room_types = $this->query_room_types();

		foreach ( $room_types as $room_type ) {
			$availability = $checker->check(
				$room_type, $this->request->get_timespan(), $this->constraints
			);

			// No rooms left, ignore this from the results.
			if ( $availability->remain_rooms()->isEmpty() ) {
				continue;
			}

			$results->push( $availability );
		}

		return $results;
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
	 * @param  array $constraints \AweBooking\Reservation\Searcher\Constraint[].
	 * @return $this
	 */
	public function set_constraints( $constraints ) {
		$this->constraints = $constraints;

		return $this;
	}

	/**
	 * Query the room-types matches for searching.
	 *
	 * @return \AweBooking\Support\Collection
	 */
	protected function query_room_types() {
		$guestcounts = $this->request->get_guest_counts();

		$wp_query_args = apply_filters( 'awebooking/reservation/query_room_types', [
			'post_type'        => Constants::ROOM_TYPE,
			'post_status'      => 'publish',
			'no_found_rows'    => true,
			'posts_per_page'   => 250,
			'booking_adults'   => $guestcounts ? $guestcounts->get_adults()->get_count() : -1,
			'booking_children' => ( $guestcounts && $guestcounts->get_children() ) ? $guestcounts->get_children()->get_count() : -1,
			'booking_infants'  => ( $guestcounts && $guestcounts->get_infants() ) ? $guestcounts->get_infants()->get_count() : -1,
		], $this );

		// Query the room types.
		$room_types = new WP_Query( $wp_query_args );

		return abrs_collect( $room_types->posts )
			->transform( function( $post ) {
				return abrs_get_room_type( $post );
			});
	}
}
