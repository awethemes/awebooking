<?php

namespace AweBooking\Support;

abstract class Service_Provider {
	/**
	 * The plugin instance.
	 *
	 * @var \AweBooking\Plugin
	 */
	protected $plugin;

	/**
	 * The hook that trigger register.
	 *
	 * @var string
	 */
	protected $when;

	/**
	 * Create a new service provider instance.
	 *
	 * @param  \AweBooking\Plugin $plugin The plugin instance.
	 * @return void
	 */
	public function __construct( $plugin ) {
		$this->plugin = $plugin;
	}

	/**
	 * Registers services on the plugin.
	 *
	 * This method should only be used to configure services and parameters.
	 */
	public function register() {}

	/**
	 * Get the hook that trigger this service provider to register.
	 *
	 * @return array|null
	 */
	public function when() {
		return $this->when ? [ $this->when, 10, 1 ] : null;
	}
}
