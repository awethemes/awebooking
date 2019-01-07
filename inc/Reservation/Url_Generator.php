<?php

namespace AweBooking\Reservation;

use AweBooking\Availability\Request;

class Url_Generator {
	/**
	 * The res request.
	 *
	 * @var \AweBooking\Availability\Request
	 */
	protected $request;

	/**
	 * Constructor.
	 *
	 * @param \AweBooking\Availability\Request $request The res request.
	 */
	public function __construct( Request $request ) {
		$this->request = $request;
	}

	/**
	 * Gets the availability URL.
	 *
	 * @param array $query Optional, extra query parameter.
	 * @return string
	 */
	public function get_availability_url( $query = [] ) {
		// TODO: Consider improve this!
		$http_request = abrs_http_request();

		if ( $keeps = $http_request->only( [ 'hotel', 'only' ] ) ) {
			$query = array_merge( $query, $keeps );
		}

		$availability_url = add_query_arg(
			array_merge( (array) $query, $this->request->to_array() ),
			abrs_get_page_permalink( 'search_results' )
		);

		return apply_filters( 'abrs_get_availability_url', $availability_url, $this->request );
	}
}
