<?php
namespace AweBooking\Reservation\Room_Stay;

use WP_Query;
use AweBooking\Constants;
use AweBooking\Reservation\Request;

class Search {
	/**
	 * The request instance.
	 *
	 * @var \AweBooking\Reservation\Request
	 */
	protected $request;

	/**
	 * Constructor.
	 *
	 * @param \AweBooking\Reservation\Request $request The request.
	 */
	public function __construct( Request $request ) {
		$this->request = $request;
	}

	/**
	 * Gets the current request.
	 *
	 * @return \AweBooking\Reservation\Request
	 */
	public function get_request() {
		return $this->request;
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
	 * Gets the search results.
	 *
	 * @param  array $constraints The constraints.
	 * @return \AweBooking\Reservation\Room_Stay\Search_Results
	 */
	public function get( $constraints = [] ) {
		$request = $this->get_request();

		$results = [];

		foreach ( $this->query_rooms() as $room_type ) {
			// In free version, we just allow user book only one plan.
			// Looking for multi rates, please upgrade to pro version :).
			$room_rate = new Room_Rate( $request->get_timespan(), $request->get_guest_counts(), $room_type, $room_type->get_standard_plan() );
			$room_rate->set_constraints( $constraints );

			$room_rate->setup();
			if ( $room_rate->has_error( 'occupancy_error' ) || $room_rate->has_error( 'no_room_left' ) ) {
				continue;
			}

			$results[] = apply_filters( 'awebooking/search_result_item', compact( 'room_type', 'room_rate' ), $room_type, $room_rate, $request );
		}

		return apply_filters( 'awebooking/search_results', new Search_Results( $this->request, $results ), $this->request );
	}

	/**
	 * Query the room types matches for searching.
	 *
	 * @return \AweBooking\Support\Collection
	 */
	public function query_rooms() {
		$guestcounts = $this->request->get_guest_counts();

		// Build the query args.
		$wp_query_args = [
			'post_type'        => Constants::ROOM_TYPE,
			'post_status'      => 'publish',
			'no_found_rows'    => true,
			'posts_per_page'   => 250, // Limit 250 items.
			'booking_adults'   => $guestcounts->get_adults()->get_count(),
			'booking_children' => $guestcounts->get_children() ? $guestcounts->get_children()->get_count() : -1,
			'booking_infants'  => $guestcounts->get_infants() ? $guestcounts->get_infants()->get_count() : -1,
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
			->transform( 'abrs_get_room_type' )
			->reject( function ( $rt ) {
				return empty( $rt ) || count( $rt->get_rooms() ) === 0;
			})->values();
	}
}
