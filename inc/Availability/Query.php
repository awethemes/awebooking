<?php
namespace AweBooking\Availability;

use WP_Error;
use WP_Query;
use AweBooking\Constants;
use AweBooking\Model\Room_Type;
use AweBooking\Model\Pricing\Rate_Plan;

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

		foreach ( $this->query_rooms() as $room_type ) {
			$room_rate = $this->room_rate( $room_type, $room_type->get_standard_plan() );

			if ( is_wp_error( $room_rate ) || $room_rate->has_error( 'no_room_left' ) ) {
				continue;
			}

			$results[] = apply_filters( 'awebooking/search_result_item', compact( 'room_type', 'room_rate' ), $this->request, $room_type, $room_rate );
		}

		return apply_filters( 'awebooking/search_results', new Query_Results( $this->request, $results ), $this->request );
	}

	/**
	 * Gets the room rate.
	 *
	 * @param Room_Type $room_type The room type instance.
	 * @param Rate_Plan $rate_plan The rate plan instance.
	 *
	 * @return Room_Rate|null
	 */
	public function room_rate( Room_Type $room_type, Rate_Plan $rate_plan ) {
		$errors = $this->precheck( $room_type, $rate_plan );

		if ( count( $errors->errors ) > 0 ) {
			return null;
		}

		$room_rate = new Room_Rate( $this->get_request(), $room_type, $rate_plan );
		$room_rate->setup();

		return $room_rate;
	}

	/**
	 * Validate the the request before create the room rate.
	 *
	 * @param Room_Type $room_type The room type instance.
	 * @param Rate_Plan $rate_plan The rate plan instance.
	 *
	 * @return WP_Error
	 */
	protected function precheck( Room_Type $room_type, Rate_Plan $rate_plan ) {
		$errors = new WP_Error;

		if ( $this->request->get_guest_counts()->get_totals() > $room_type->get( 'maximum_occupancy' ) ) {
			$errors->add( 'overflow_occupancy', esc_html__( 'Error: Maximum occupancy.', 'awebooking' ) );
		}

		do_action( 'awebooking/reservation/precheck', $errors, $this->get_request(), $room_type, $rate_plan, $this );

		return $errors;
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
			'booking_children' => $guestcounts->has( 'children' ) ? $guestcounts->get_children()->get_count() : -1,
			'booking_infants'  => $guestcounts->has( 'infants' ) ? $guestcounts->get_infants()->get_count() : -1,
		];

		// Filter get only room types.
		if ( ! empty( $this->request['only'] ) ) {
			$wp_query_args['post__in'] = wp_parse_id_list( $this->request['only'] );
		}

		if ( ! empty( $this->request['hotel'] ) ) {
			$wp_query_args['post_parent'] = absint( $this->request['hotel'] );
		}

		// Perform query room types.
		$room_types = new WP_Query(
			apply_filters( 'awebooking/reservation/query_room_types', $wp_query_args, $this )
		);

		return abrs_collect( $room_types->posts )
			->transform( 'abrs_get_room_type' )
			->reject( function ( Room_Type $rt ) {
				return empty( $rt ) || count( $rt->get_rooms() ) === 0;
			})->values();
	}
}
