<?php

namespace AweBooking\Frontend\Search;

use AweBooking\Availability\Request;
use AweBooking\Availability\Constraints\Reservation_Constraint;

class Search_Query {
	/**
	 * The res request instance.
	 *
	 * @var \AweBooking\Availability\Request
	 */
	public $res_request;

	/**
	 * The results.
	 *
	 * @var \AweBooking\Availability\Query_Results
	 */
	public $results;

	/**
	 * The errors.
	 *
	 * @var \WP_Error
	 */
	public $errors;

	/**
	 * Constructor.
	 *
	 * @param \AweBooking\Availability\Request $res_request
	 */
	public function __construct( Request $res_request ) {
		$this->res_request = $res_request;
	}

	/**
	 * Init the search query.
	 *
	 * @return void
	 */
	public function init() {
		if ( ! did_action( 'wp' ) ) {
			return;
		}

		if ( abrs_is_search_page() ) {
			$this->setup_res_request();

			$this->search_rooms();
			$this->register_globals();

			// Tell others that can not cache this page.
			abrs_nocache_headers();
		}
	}

	/**
	 * Setup the res request.
	 *
	 * @return void
	 */
	public function setup_res_request() {
		$validate = $this->res_request->validate();

		if ( is_wp_error( $validate ) ) {
			$this->errors = $validate;
			return;
		}

		// TODO: ...
		abrs_reservation()->set_current_request( $this->res_request );

		awebooking()->instance( Request::class, $this->res_request );

		do_action( 'setup_res_request', $this->res_request );
	}

	/**
	 * Perform search rooms.
	 *
	 * @return void
	 */
	public function search_rooms() {
		do_action( 'abrs_prepare_search', $this );

		if ( $this->is_error() ) {
			return;
		}

		$reservation = abrs_reservation();

		$contraints = apply_filters( 'abrs_search_contraints', [
			new Reservation_Constraint( $reservation ),
		] );

		$this->results = $this->res_request
			->add_contraints( $contraints )
			->search();

		// TODO: ...
		$http_request = $this->res_request->get_http_request();

		switch ( $http_request->get( 'sortby', 'cheapest' ) ) {
			case 'cheapest':
				$this->results->items = $this->results->get_items()->sortBy(function ( $item ) {
					return $item['room_rate']->get_rate();
				});
				break;

			case 'highest':
				$this->results->items = $this->results->get_items()->sortByDesc(function ( $item ) {
					return $item['room_rate']->get_rate();
				});
				break;
		}

		do_action( 'abrs_search_complete', $this );
	}

	/**
	 * Return true if we got any errors.
	 *
	 * @return bool
	 */
	public function is_error() {
		return is_wp_error( $this->errors ) && count( $this->errors->errors ) > 0;
	}

	/**
	 * Setup the search results to globals.
	 *
	 * @return void
	 */
	public function register_globals() {
		$GLOBALS['abrs_query']   = $this;
		$GLOBALS['res_request']  = $this->res_request;
		$GLOBALS['abrs_results'] = $this->results;
	}

	/**
	 * Gets the plugin instance.
	 *
	 * @return \AweBooking\Plugin
	 */
	public function get_plugin() {
		_deprecated_function( __FUNCTION__, '3.2.0', null );

		return awebooking();
	}

	/**
	 * Gets the http request instance.
	 *
	 * @return \Awethemes\Http\Request
	 */
	public function get_request() {
		_deprecated_function( __FUNCTION__, '3.2.0', null );

		return abrs_http_request();
	}
}
