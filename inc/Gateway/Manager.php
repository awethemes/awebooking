<?php
namespace AweBooking\Gateway;

use AweBooking\Plugin;
use AweBooking\Support\Collection;

class Manager {
	/**
	 * The plugin instance.
	 *
	 * @var \AweBooking\Plugin
	 */
	protected $plugin;

	/**
	 * The gateways store.
	 *
	 * @var \AweBooking\Support\Collection
	 */
	protected $gateways;

	/**
	 * Constructor.
	 *
	 * @param \AweBooking\Plugin $plugin   The plugin instance.
	 * @param array              $gateways Optional, the gateways.
	 */
	public function __construct( Plugin $plugin, $gateways = [] ) {
		$this->plugin   = $plugin;
		$this->gateways = new Collection;

		foreach ( $gateways as $gateway ) {
			$this->register( is_string( $gateway ) ? $this->plugin->make( $gateway ) : $gateway );
		}
	}

	/**
	 * Get a gateway in enabled only.
	 *
	 * @param  string $gateway The gateway ID.
	 * @return \AweBooking\Gateway\Gateway
	 */
	public function get( $gateway ) {
		return $this->enabled()->get( $gateway );
	}

	/**
	 * Returns all gateways.
	 *
	 * @return \AweBooking\Support\Collection
	 */
	public function all() {
		return $this->gateways;
	}

	/**
	 * Returns all gateways enabled only.
	 *
	 * @return \AweBooking\Support\Collection
	 */
	public function enabled() {
		return $this->gateways->filter( function( $gateway ) {
			return $gateway->is_enabled();
		});
	}

	/**
	 * Add a new reservation gateway.
	 *
	 * @param  \AweBooking\Gateway\Gateway $gateway The gateway implementation.
	 * @return $this
	 */
	public function register( Gateway $gateway ) {
		$gateway->setup();

		$this->gateways[ $gateway->get_method() ] = $gateway;

		return $this;
	}

	/**
	 * Determines a gateway has been registered.
	 *
	 * @param  string $gateway The gateway ID.
	 * @return bool
	 */
	public function registered( $gateway ) {
		$gateway = $this->parse_gateway_method( $gateway );

		return array_key_exists( $gateway, $this->gateways );
	}

	/**
	 * Parse the gateway method name.
	 *
	 * @param  mixed $gateway The gateway.
	 * @return string
	 */
	protected function parse_gateway_method( $gateway ) {
		if ( $gateway instanceof Gateway ) {
			return $gateway->get_method();
		}

		return $gateway;
	}
}
