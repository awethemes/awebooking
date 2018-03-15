<?php
namespace AweBooking\Reservation\Searcher;

use AweBooking\Model\Factory;
use AweBooking\Support\Utils as U;
use AweBooking\Reservation\Request;
use AweBooking\Model\Room_Type;

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
	 * Get the availability results.
	 *
	 * @return \AweBooking\Reservation\Searcher\Results
	 */
	public function get() {
		$results = new Results;
		$checker = new Checker;

		$room_types = $this->search_room_types();

		foreach ( $room_types as $room_type ) {
			$availability = $checker->check( $room_type, $this->request->get_timespan() );

			$availability->apply_constraints( $this->constraints );

			$results->push( $availability );
		}

		return $results;
	}

	/**
	 * Query the room-types matches for searching.
	 *
	 * @return \AweBooking\Support\Collection
	 */
	protected function search_room_types() {
		$guestcounts = $this->request->get_guest_counts();

		$queryargs = apply_filters( 'awebooking/reservation/query_room_types', [
			'post_status'      => 'publish',
			'posts_per_page'   => 256,
			'booking_adults'   => $guestcounts ? $guestcounts->get_adults() : -1,
			'booking_children' => $guestcounts ? $guestcounts->get_children() : -1,
			'booking_infants'  => $guestcounts ? $guestcounts->get_infants() : -1,
		], $this );

		$room_types = Room_Type::query( $queryargs )->posts;

		return U::collect( $room_types )
			->map( function( $post ) {
				return Factory::get_room_type( $post );
			});
	}
}
