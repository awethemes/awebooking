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
		$this->plugin->singleton( 'gateways', function() {
			return new Manager( $this->plugin, $this->gateways );
		});

		$this->plugin->alias( 'gateways', Manager::class );
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
