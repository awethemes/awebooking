<?php

namespace AweBooking\Support;

abstract class Manager implements \ArrayAccess {
	/**
	 * The plugin instance.
	 *
	 * @var \AweBooking\Plugin
	 */
	protected $plugin;

	/**
	 * The array of "drivers".
	 *
	 * @var \AweBooking\Support\Collection|array
	 */
	protected $drivers = [];

	/**
	 * Create a new manager instance.
	 *
	 * @param  \AweBooking\Plugin $plugin The plugin instance.
	 * @return void
	 */
	public function __construct( $plugin ) {
		$this->plugin = $plugin;
	}

	/**
	 * Get all of the drivers.
	 *
	 * @return array
	 */
	public function all() {
		return $this->drivers;
	}

	/**
	 * Get all of the registered "drivers".
	 *
	 * @return \AweBooking\Support\Collection|array
	 */
	public function get_drivers() {
		return $this->drivers;
	}

	/**
	 * Handle register a new driver.
	 *
	 * @param  mixed $driver The driver implementation.
	 * @return bool
	 */
	abstract public function register( $driver );

	/**
	 * Determines a driver has been registered.
	 *
	 * @param  string $driver The driver key name.
	 * @return bool
	 */
	public function registered( $driver ) {
		return isset( $this->drivers[ $driver ] );
	}

	/**
	 * Get a driver instance.
	 *
	 * @param  string $driver The driver key name.
	 * @return mixed
	 */
	public function get( $driver ) {
		return $this->driver( $driver );
	}

	/**
	 * Get a driver instance.
	 *
	 * @param  string $driver The driver key name.
	 * @return mixed
	 *
	 * @throws \InvalidArgumentException
	 */
	public function driver( $driver ) {
		if ( ! isset( $this->drivers[ $driver ] ) ) {
			throw new \InvalidArgumentException( "Driver [$driver] not supported." );
		}

		return $this->drivers[ $driver ];
	}

	/**
	 * Determine if the given offset exists.
	 *
	 * @param  string $offset The offset key.
	 * @return bool
	 */
	public function offsetExists( $offset ) {
		return $this->registered( $offset );
	}

	/**
	 * Get the driver for a given offset.
	 *
	 * @param  string $offset The offset key.
	 * @return mixed
	 */
	public function offsetGet( $offset ) {
		return $this->driver( $offset );
	}

	/**
	 * Set the driver at the given offset.
	 *
	 * @param  string $offset The offset key.
	 * @param  mixed  $driver The offset driver.
	 * @return void
	 */
	public function offsetSet( $offset, $driver ) {
		// ...
	}

	/**
	 * Unset the driver at the given offset.
	 *
	 * @param  string $offset The offset key.
	 * @return void
	 */
	public function offsetUnset( $offset ) {
		// ...
	}
}
