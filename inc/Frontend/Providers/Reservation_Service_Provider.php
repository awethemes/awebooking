<?php

namespace AweBooking\Frontend\Providers;

use AweBooking\Availability\Request;
use AweBooking\Checkout\Checkout;
use AweBooking\Frontend\Search\Search_Query;
use AweBooking\Reservation\Reservation;
use AweBooking\Reservation\Storage\Session_Store;
use AweBooking\Support\Service_Provider;

class Reservation_Service_Provider extends Service_Provider {
	/**
	 * Registers services on the plugin.
	 *
	 * @return void
	 */
	public function register() {
		$this->plugin->singleton( 'reservation.store', function() {
			return new Session_Store( $this->plugin['session.store'] );
		});

		$this->plugin->singleton( 'reservation', function() {
			return new Reservation( $this->plugin['reservation.store'] );
		});

		$this->plugin->singleton( 'checkout', function() {
			return new Checkout( $this->plugin['gateways'], $this->plugin['session'], $this->plugin['reservation'] );
		});

		$this->plugin->alias( 'checkout', Checkout::class );
		$this->plugin->alias( 'reservation', Reservation::class );

		$this->plugin->alias( Request::class, 'res_request' );
		$this->plugin->alias( Request::class, 'reservation.request' );
	}

	/**
	 * Init (boot) the service provider.
	 *
	 * @return void
	 */
	public function init() {
		if ( is_admin() ) {
			return;
		}

		// Init the reservation hooks.
		$this->plugin->make( 'reservation' )->init();

		// Init the search rooms.
		add_action( 'wp', [ $this, 'init_search_rooms' ] );
	}

	/**
	 * Init the search query the "search results" page.
	 *
	 * @access private
	 */
	public function init_search_rooms() {
		// This action work only on search page.
		if ( ! abrs_is_search_page() ) {
			return;
		}

		$request = Request::create_from_request(
			$this->plugin->make( 'request' )
		);

		( new Search_Query( $request ) )->init();
	}
}
