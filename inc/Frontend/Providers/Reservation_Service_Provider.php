<?php
namespace AweBooking\Frontend\Providers;

use AweBooking\Reservation\Reservation;
use AweBooking\Frontend\Checkout\Checkout;
use AweBooking\Support\Service_Provider;

class Reservation_Service_Provider extends Service_Provider {
	/**
	 * Registers services on the plugin.
	 *
	 * @return void
	 */
	public function register() {
		$this->plugin->singleton( 'reservation', function() {
			return new Reservation( $this->plugin['session'] );
		});

		$this->plugin->singleton( 'checkout', function() {
			return new Checkout( $this->plugin['session'], $this->plugin['reservation'] );
		});

		$this->plugin->alias( 'checkout', Checkout::class );
		$this->plugin->alias( 'reservation', Reservation::class );
	}

	/**
	 * Init (boot) the service provider.
	 *
	 * @return void
	 */
	public function init() {
		$this->plugin->make( 'reservation' );

		add_action( 'template_redirect', [ $this, 'setup_res_request' ] );
	}

	/**
	 * Setup the reservation request on the "search results" page.
	 *
	 * @access private
	 */
	public function setup_res_request() {
		global $wp;

		// This action work only on search page.
		if ( ! abrs_is_search_page() ) {
			return;
		}

		// Resolve the htp request, if the request is not "shared",
		// we will set it as "shared" in the container.
		$request = $this->plugin->make( 'request' );

		if ( ! $this->plugin->isShared( 'request' ) ) {
			$this->plugin->instance( 'request', $request );
		}

		// Set the "res_request" into the query vars,
		// we can retrieve it late (in the shortcode).
		if ( $request->filled( 'check-in', 'check-out' ) ) {
			$res_request = abrs_create_res_request( $request );

			if ( ! is_wp_error( $res_request ) ) {
				$this->plugin['reservation']->set_current_request( $res_request );
			}

			$wp->set_query_var( 'res_request', $res_request );
		}
	}
}
