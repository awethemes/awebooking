<?php
namespace AweBooking\Providers;

use AweBooking\Gateway\Gateways;
use AweBooking\Support\Service_Provider;

class Payment_Service_Provider extends Service_Provider {
	/**
	 * Core gateways will be registers.
	 *
	 * @var array
	 */
	protected $core_gateways = [
		\AweBooking\Gateway\Direct_Payment_Gateway::class,
		\AweBooking\Gateway\BACS_Gateway::class,
	];

	/**
	 * Registers services on the plugin.
	 *
	 * @return void
	 */
	public function register() {
		$this->plugin->singleton( 'gateways', function() {
			return new Gateways( $this->plugin, $this->get_sorted_gateways() );
		});

		$this->plugin->alias( 'gateways', Gateways::class );
	}

	/**
	 * Init (boot) the service provider.
	 *
	 * @return void
	 */
	public function init() {
		$gateways = $this->plugin->make( 'gateways' );

		do_action( 'awebooking/setup_gateways', $gateways );

		$gateways->init();
	}

	/**
	 * Returns the list gateways for register.
	 *
	 * @return array
	 */
	protected function get_sorted_gateways() {
		$ordering = (array) $this->plugin->get_option( 'list_gateway_order', [] );

		// Filters the payment gateways classmap.
		$load_gateways = apply_filters( 'awebooking/payment_gateways', $this->core_gateways );

		$sorted = [];
		$bottom = 1001;

		foreach ( $load_gateways as $gateway ) {
			$gateway = is_string( $gateway ) ? $this->plugin->make( $gateway ) : $gateway;

			// Found the possiton in $ordering.
			$index = array_search( $gateway->get_method(), $ordering );

			// Found in $ordering, just add by that position,
			// otherwise we will add to end of the $sorted.
			if ( false !== $index ) {
				$sorted[ $index ] = $gateway;
			} else {
				$sorted[ $bottom ] = $gateway;
				$bottom++;
			}
		}

		// Sort by index.
		ksort( $sorted );

		return array_values( $sorted );
	}
}
