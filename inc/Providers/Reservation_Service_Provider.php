<?php
namespace AweBooking\Providers;

use AweBooking\Reservation\Session;
use AweBooking\Model\Source;
use AweBooking\Reservation\Source\Store;
use AweBooking\Reservation\Source\WP_Options_Store;
use AweBooking\Reservation\Source\Manager;
use AweBooking\Reservation\Source\Mapping;
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

		$this->awebooking->singleton( Store::class, WP_Options_Store::class );
		$this->awebooking->alias( Store::class, 'source_store' );

		$this->awebooking->singleton( 'reservation_sources', function() {
			return new Manager;
		});

		$this->awebooking->alias( 'reservation_sources', Manager::class );

		$this->awebooking['primary_sources'] = $this->get_primary_sources();
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

	/**
	 * [register_reservation_sources description]
	 *
	 * @param  [type] $sources [description]
	 * @return [type]
	 */
	protected function register_reservation_sources( $sources ) {
		$store = $this->awebooking->make( Store::class );

		$sources->register(
			new Source( 'direct_website', esc_html_x( 'Website', 'reservation source', 'awebooking' ) )
		);

		$sources->register(
			new Source( 'direct_walk_in', esc_html_x( 'Walk-In', 'reservation source', 'awebooking' ) )
		);

		$sources->register(
			new Source( 'direct_phone', esc_html_x( 'Phone', 'reservation source', 'awebooking' ) )
		);

		$sources->register(
			new Source( 'direct_email', esc_html_x( 'Email', 'reservation source', 'awebooking' ) )
		);

		Mapping::map( $sources, $store );
	}

	/**
	 * The primary sources.
	 *
	 * @return array
	 */
	protected function get_primary_sources() {
		return [
			'direct_website' => [
				'uid'        => 'direct_website',
				'name'       => esc_html_x( 'Website', 'reservation source', 'awebooking' ),
			],
			'direct_walk_in' => [
				'uid'        => 'direct_walk_in',
				'name'       => esc_html_x( 'Walk-In', 'reservation source', 'awebooking' ),
			],
			'direct_phone' => [
				'uid'        => 'direct_phone',
				'name'       => esc_html_x( 'Phone', 'reservation source', 'awebooking' ),
			],
			'direct_email' => [
				'uid'        => 'direct_email',
				'name'       => esc_html_x( 'Email', 'reservation source', 'awebooking' ),
			],
		];
	}
}
