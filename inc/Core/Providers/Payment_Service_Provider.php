<?php

namespace AweBooking\Core\Providers;

use AweBooking\Gateway\Gateways;
use AweBooking\Support\Service_Provider;

class Payment_Service_Provider extends Service_Provider {
	/**
	 * Core gateways will be registers.
	 *
	 * @var array
	 */
	protected $gateways = [
		\AweBooking\Gateway\Direct_Payment_Gateway::class,
		\AweBooking\Gateway\BACS_Gateway::class,
	];

	/**
	 * Registers services on the plugin.
	 *
	 * @return void
	 */
	public function register() {
		foreach ( $this->gateways as $gateway ) {
			$this->plugin->singleton( $gateway );
		}

		$this->plugin->singleton( 'gateways', function() {
			return new Gateways( $this->plugin );
		});

		$this->plugin->tag( $this->gateways, 'gateways' );
		$this->plugin->alias( 'gateways', Gateways::class );
	}

	/**
	 * Init (boot) the service provider.
	 *
	 * @return void
	 */
	public function init() {
		add_action( 'wp_loaded', function () {
			$gateways = $this->plugin->make( 'gateways' );

			foreach ( $this->plugin->tagged( 'gateways' ) as $gateway ) {
				$gateways->register( $gateway );
			}

			do_action( 'abrs_setup_gateways', $gateways );
		});
	}
}
