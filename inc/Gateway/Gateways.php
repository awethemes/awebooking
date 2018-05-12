<?php
namespace AweBooking\Gateway;

use AweBooking\Plugin;
use AweBooking\Support\Manager;
use AweBooking\Support\Collection;

class Gateways extends Manager {
	/**
	 * Constructor.
	 *
	 * @param \AweBooking\Plugin $plugin   The plugin instance.
	 * @param array              $gateways Optional, the gateways.
	 */
	public function __construct( Plugin $plugin, $gateways = [] ) {
		$this->plugin  = $plugin;
		$this->drivers = new Collection;

		foreach ( $gateways as $gateway ) {
			$this->register( is_string( $gateway ) ? $this->plugin->make( $gateway ) : $gateway );
		}
	}

	/**
	 * Init the mailer (trigger in `init`).
	 *
	 * @return void
	 */
	public function init() {
		// ...
	}

	/**
	 * Get a gateway (enable only).
	 *
	 * @param  string $gateway The gateway ID.
	 * @return \AweBooking\Gateway\Gateway|null
	 */
	public function get( $gateway ) {
		return $this->enabled()->get( $gateway );
	}

	/**
	 * Returns gateways enable only.
	 *
	 * @return \AweBooking\Support\Collection
	 */
	public function enabled() {
		return $this->all()->filter( function( $gateway ) {
			return $gateway->is_enabled();
		});
	}

	/**
	 * Add a new reservation gateway.
	 *
	 * @param  \AweBooking\Gateway\Gateway $gateway The gateway implementation.
	 * @return bool
	 *
	 * @throws \InvalidArgumentException
	 */
	public function register( $gateway ) {
		if ( ! $gateway instanceof Gateway ) {
			throw new \InvalidArgumentException( 'Gateway must be a class instance of AweBooking\Gateway\Gateway.' );
		}

		$key = $gateway->get_method();
		if ( empty( $key ) || $this->registered( $key ) ) {
			return false;
		}

		$gateway->setup();
		$this->drivers[ $key ] = $gateway;

		return true;
	}
}
