<?php
namespace AweBooking\Providers;

use AweBooking\Gateway\Gateways;
use AweBooking\Support\Service_Provider;

class Payment_Service_Provider extends Service_Provider {
	/**
	 * Registers services on the plugin.
	 *
	 * @return void
	 */
	public function register() {
		$this->plugin->singleton( 'gateways', function() {
			return new Gateways( $this->plugin );
		});

		$this->plugin->alias( 'gateways', Gateways::class );
	}

	/**
	 * Init (boot) the service provider.
	 *
	 * @return void
	 */
	public function init() {
		$this->plugin['gateways']->init();
	}
}
