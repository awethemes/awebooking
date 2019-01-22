<?php

namespace AweBooking\Support\Traits;

trait Singleton {
	/**
	 * The Singleton instance.
	 *
	 * @var static
	 */
	protected static $instance;

	/**
	 * Returns the "Singleton" instance.
	 *
	 * @return static
	 */
	final public static function get_instance() {
		if ( is_null( static::$instance ) ) {
			static::$instance = new static;
		}

		return static::$instance;
	}

	/**
	 * Cloning is forbidden.
	 *
	 * @return void
	 */
	final private function __clone() {}

	/**
	 * Unserializing instances of this class is forbidden.
	 *
	 * @return void
	 */
	final private function __wakeup() {}
}
