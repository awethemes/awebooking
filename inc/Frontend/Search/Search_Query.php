<?php

namespace AweBooking\Frontend\Search;

use AweBooking\Plugin;
use Awethemes\Http\Request;

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

		// Create the res request.
		$res_request = abrs_create_res_request( $request, true );

		if ( is_wp_error( $res_request ) ) {
			$this->errors = $res_request;
			return;
		}

		$reservation = $this->plugin->make( 'reservation' );
		$previous_request = $reservation->get_previous_request();

		// Flush the reservation when session request & current request is different.
		if ( $previous_request && ! $res_request->same_with( $previous_request ) ) {
			$reservation->flush();
		}

		$this->res_request = $res_request;
		$reservation->set_current_request( $res_request );
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

		$this->results = $this->res_request
			->add_contraints( apply_filters( 'abrs_contraints', [] ) )
			->search();

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
