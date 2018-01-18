<?php
namespace AweBooking\Providers;

use AweBooking\Reservation\Session;
use AweBooking\Reservation\Source\Direct;
use AweBooking\Reservation\Source\Manager;
use AweBooking\Support\Service_Provider;

class Reservation_Service_Provider extends Service_Provider {
	/**
	 * Registers services on the AweBooking.
	 *
	 * @return void
	 */
	public function register() {
		$this->awebooking->singleton( 'reservation_session', function( $a ) {
			return new Session( $a['session'] );
		});

		$this->awebooking->singleton( 'reservation_admin_session', function( $a ) {
			return new Session( $a['session'], Session::ADMIN_GROUP );
		});

		$this->awebooking->singleton( 'reservation_sources', function() {
			return new Manager;
		});

		$this->awebooking->alias( 'reservation_sources', Manager::class );
	}

	/**
	 * Init (boot) the service provider.
	 *
	 * @return void
	 */
	public function init() {
		$this->register_reservation_sources(
			$this->awebooking->make( 'reservation_sources' )
		);
	}

	protected function register_reservation_sources( $source_manager ) {
		$source_manager->register(
			new Direct( 'direct_website', esc_html_x( 'Website', 'reservation source', 'awebooking' ) )
		);

		$source_manager->register(
			new Direct( 'direct_walk_in', esc_html_x( 'Walk-In', 'reservation source', 'awebooking' ) )
		);

		$source_manager->register(
			new Direct( 'direct_phone', esc_html_x( 'Phone', 'reservation source', 'awebooking' ) )
		);

		$source_manager->register(
			new Direct( 'direct_email', esc_html_x( 'Email', 'reservation source', 'awebooking' ) )
		);
	}
}
