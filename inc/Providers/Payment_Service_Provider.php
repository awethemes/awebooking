<?php
namespace AweBooking\Providers;

use AweBooking\Gateway\Manager;
use AweBooking\Support\Service_Provider;

class Payment_Service_Provider extends Service_Provider {
	/**
	 * The gateways will be registers.
	 *
	 * @var array
	 */
	protected $gateways = [
		\AweBooking\Gateway\Check_Payment_Gateway::class,
		\AweBooking\Gateway\BACS_Gateway::class,
	];

	/**
	 * Registers services on the AweBooking.
	 *
	 * @return void
	 */
	public function register() {
		// Binding the gateways manager.
		$this->awebooking->singleton( 'gateways', function() {
			// Make class instance of gateways classes.
			$gateways = array_map( function( $gateway ) {
				return $this->awebooking->make( $gateway );
			}, $this->gateways );

			return new Manager( $gateways );
		});

		$this->awebooking->alias( 'gateways', Manager::class );
	}

	/**
	 * Init (boot) the service provider.
	 *
	 * @return void
	 */
	public function init() {
		// ...
	}
}
