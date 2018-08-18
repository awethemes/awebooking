<?php

namespace AweBooking\Frontend\Search;

use AweBooking\Plugin;
use Awethemes\Http\Request;
use AweBooking\Availability\Constraints\Reservation_Constraint;

class Search_Query {
	/**
	 * The plugin instance.
	 *
	 * @var \AweBooking\Plugin
	 */
	protected $plugin;

	/**
	 * The http request instance.
	 *
	 * @var \Awethemes\Http\Request
	 */
	protected $request;

	/**
	 * The errors.
	 *
	 * @var \WP_Error
	 */
	public $errors;

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
	 * Constructor.
	 *
	 * @param \AweBooking\Plugin $plugin The plugin instance.
	 */
	public function __construct( Plugin $plugin ) {
		$this->plugin = $plugin;
	}

	/**
	 * Prepare the query.
	 *
	 * @param \Awethemes\Http\Request $request The request instance.
	 * @return $this
	 */
	public function prepare( Request $request ) {
		$this->request = $request;

		$this->plugin->instance( 'request', $request );

		return $this;
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
		$request = $this->get_request();

		if ( ! $request->filled( 'check_in', 'check_out' ) &&
			 ! $request->filled( 'check-in', 'check-out' ) ) {
			return;
		}

		// Create the res_request.
		$res_request = abrs_create_res_request( $request, true );

		if ( is_wp_error( $res_request ) ) {
			$this->errors = $res_request;
			return;
		}

		// Setup the res_request.
		$this->res_request = $res_request;
		abrs_reservation()->set_current_request( $this->res_request );

		do_action( 'setup_res_request', $res_request );
	}

	/**
	 * Perform search rooms.
	 *
	 * @return \AweBooking\Availability\Query_Results|null
	 */
	public function search_rooms() {
		do_action( 'abrs_prepare_search', $this );

		if ( ! $this->res_request ) {
			return null;
		}

		$request = $this->get_request();
		$this->res_request['query_args'] = $request->only( 'hotel', 'only' );

		$contraints = apply_filters( 'abrs_search_contraints', [
			new Reservation_Constraint( $this->plugin['reservation'] ),
		]);

		$this->results = $this->res_request
			->add_contraints( $contraints )
			->search();

		// TODO: ...
		switch ( $request->get( 'sortby', 'cheapest' ) ) {
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

		return $this->results;
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
	 * Return true if we got any errors.
	 *
	 * @return bool
	 */
	public function is_error() {
		return is_wp_error( $this->errors ) && count( $this->errors->errors ) > 0;
	}

	/**
	 * Gets the plugin instance.
	 *
	 * @return \AweBooking\Plugin
	 */
	public function get_plugin() {
		return $this->plugin;
	}

	/**
	 * Gets the http request instance.
	 *
	 * @return \Awethemes\Http\Request
	 */
	public function get_request() {
		return ! is_null( $this->request )
			? $this->request
			: $this->plugin->make( 'request' );
	}
}
