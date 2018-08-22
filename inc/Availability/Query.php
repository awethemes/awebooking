<?php
namespace AweBooking\Availability;

use WP_Query;
use AweBooking\Constants;
use AweBooking\Model\Room_Type;

class Query {
	/**
	 * The request instance.
	 *
	 * @var \AweBooking\Availability\Request
	 */
	protected $request;

	/**
	 * Constructor.
	 *
	 * @param \AweBooking\Availability\Request $request The request.
	 */
	public function __construct( Request $request ) {
		$this->request = $request;
	}

	/**
	 * Gets the current request.
	 *
	 * @return \AweBooking\Availability\Request
	 */
	public function get_request() {
		return $this->request;
	}

	/**
	 * Gets the search results.
	 *
	 * @return \AweBooking\Availability\Query_Results
	 */
	public function search() {
		$results = [];

		$room_types = $this->query_rooms();

		do_action( 'abrs_prepare_search_rooms', $room_types, $this->request );

		foreach ( $room_types as $room_type ) {
			$room_rate = abrs_retrieve_room_rate([
				'request'   => $this->get_request(),
				'room_type' => $room_type,
				'rate_plan' => abrs_get_base_rate( $room_type ),
			]);

			// This filter let users modify the room rate before it can be rejected.
			$room_rate = apply_filters( 'abrs_search_room_rate', $room_rate, $this->request );

			// Ignore invalid room rates.
			if ( is_wp_error( $room_rate ) || ! $room_rate->is_visible() ) {
				continue;
			}

			$results[] = apply_filters( 'abrs_search_result_item', compact( 'room_type', 'room_rate' ), $this->request );
		}

		return apply_filters( 'abrs_search_results', new Query_Results( $this->request, $results ), $this->request );
	}

	/**
	 * Query the room types matches for searching.
	 *
	 * @return \AweBooking\Support\Collection
	 */
	public function query_rooms() {
		$guestcounts = $this->request->get_guest_counts();

		// Gets the query args.
		$query_args = (array) $this->request['query_args'];

		// Build the query args.
		$wp_query_args = [
			'post_type'        => Constants::ROOM_TYPE,
			'post_status'      => 'publish',
			'no_found_rows'    => true,
			'posts_per_page'   => 250, // Limit 250 items.
			'booking_adults'   => $guestcounts->get_adults()->get_count(),
			'booking_children' => $guestcounts->has( 'children' ) ? $guestcounts->get_children()->get_count() : -1,
			'booking_infants'  => $guestcounts->has( 'infants' ) ? $guestcounts->get_infants()->get_count() : -1,
			'meta_query'       => [],
		];

		// Filter get only room types.
		if ( ! empty( $query_args['only'] ) ) {
			$wp_query_args['post__in'] = wp_parse_id_list( $query_args['only'] );
		}

		if ( abrs_multiple_hotels() ) {
			if ( ! empty( $query_args['hotel'] ) ) {
				$wp_query_args['meta_query'][] = [
					'key'     => '_hotel_id',
					'value'   => absint( $query_args['hotel'] ),
					'type'    => 'numeric',
					'compare' => '=',
				];
			} else {
				$wp_query_args['meta_query'][] = [
					'relation' => 'OR',
					[
						'key'     => '_hotel_id',
						'compare' => 'NOT EXISTS',
					],
					[
						'key'     => '_hotel_id',
						'value'   => '0',
						'type'    => 'numeric',
						'compare' => '=',
					],
				];
			}
		}

		// Perform query room types.
		$room_types = ( new WP_Query(
			apply_filters( 'abrs_query_room_types', $wp_query_args, $this->request )
		) )->posts;

		// Prime caches to reduce future queries.
		abrs_prime_room_caches( wp_list_pluck( $room_types, 'ID' ) );

		return abrs_collect( $room_types )
			->transform( 'abrs_get_room_type' )
			->reject( function ( Room_Type $rt ) {
				return empty( $rt ) || count( $rt->get_rooms() ) === 0;
			})->values();
	}
}
