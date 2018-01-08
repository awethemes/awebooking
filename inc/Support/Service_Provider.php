<?php
namespace AweBooking\Support;

abstract class Service_Provider {
	/**
	 * The AweBooking instance.
	 *
	 * @var \AweBooking\AweBooking
	 */
	protected $awebooking;

	/**
	 * The hook that trigger to register.
	 *
	 * @var string
	 */
	protected $when = '';

	/**
	 * Create a new service provider instance.
	 *
	 * @param  \AweBooking\AweBooking $awebooking The AweBooking instance.
	 * @return void
	 */
	public function __construct( $awebooking ) {
		$this->awebooking = $awebooking;
	}

	/**
	 * Registers services on the AweBooking.
	 *
	 * This method should only be used to configure services and parameters.
	 */
	// public function register() {}

	/**
	 * Init (boot) the service provider.
	 *
	 * @return void
	 */
	// public function init() {}

	/**
	 * Get the hook that trigger this service provider to register.
	 *
	 * @return array|null
	 */
	public function when() {
		return $this->when ? [ $this->when, 10, 1 ] : null;
	}

	/**
	 * Determine if the provider is deferred.
	 *
	 * @return bool
	 */
	public function has_when() {
		return (bool) $this->when;
	}

	/**
	 * Set the awebooking instance.
	 *
	 * @param AweBooking $instance The awebooking instance.
	 */
	public function set_awebooking( $instance ) {
		$this->awebooking = $instance;
	}

	/**
	 * Get the awebooking instance.
	 *
	 * @return AweBooking
	 */
	public function get_awebooking() {
		return $this->awebooking;
	}
}
