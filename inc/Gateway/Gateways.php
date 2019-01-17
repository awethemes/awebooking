<?php

namespace AweBooking\Gateway;

use AweBooking\Plugin;
use AweBooking\Support\Manager;

class Gateways extends Manager {
	/**
	 * Constructor.
	 *
	 * @param \AweBooking\Plugin $plugin The plugin instance.
	 */
	public function __construct( Plugin $plugin ) {
		$this->drivers = new Gateway_Collection;

		parent::__construct( $plugin );
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

	/**
	 * Get a gateway (enable only).
	 *
	 * @param  string $gateway The gateway ID.
	 * @return \AweBooking\Gateway\Gateway|null
	 */
	public function get( $gateway ) {
		if ( ! $this->registered( $gateway ) ) {
			return null;
		}

		/* @var $gateway \AweBooking\Gateway\Gateway */
		$gateway = $this->driver( $gateway );

		if ( ! $gateway->is_enabled() ) {
			return null;
		}

		return $gateway;
	}

	/**
	 * Returns the list sorted gateways.
	 *
	 * @return \AweBooking\Gateway\Gateway_Collection
	 */
	public function get_sorted() {
		return $this->drivers->sorted();
	}

	/**
	 * Returns gateways enable only.
	 *
	 * @param bool $sorted With sorted gateways.
	 *
	 * @return \AweBooking\Support\Collection
	 */
	public function get_enabled( $sorted = true ) {
		return $this->drivers->enabled( $sorted );
	}
}
