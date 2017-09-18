<?php
namespace AweBooking\Support;

trait Services_Hooks_Trait {
	/**
	 * Determine if container is booted.
	 *
	 * @var boolean
	 */
	protected $booted = false;

	/**
	 * The names of the loaded service hooks.
	 *
	 * @var array
	 */
	protected $loaded_hooks = array();

	/**
	 * All of the registered service hooks.
	 *
	 * @var array
	 */
	protected $service_hooks = array();

	/**
	 * Trigger a service hooks.
	 *
	 * @param  Service_Hooks $hook   A Service_Hooks instance.
	 * @param  array         $values An array of values that customizes the hook.
	 * @return $this
	 */
	public function trigger( Service_Hooks $hook, array $values = array() ) {
		$class_name = get_class( $hook );

		if ( $this->get_registered( $class_name ) ) {
			return $this;
		}

		// Register the hook.
		$hook->register( $this );
		foreach ( $values as $key => $value ) {
			$this[ $key ] = $value;
		}

		if ( $this->is_booted() ) {
			$this->init_service_hooks( $hook );
		}

		// Mark the given hook as registered.
		$this->service_hooks[] = $hook;
		$this->loaded_hooks[ $class_name ] = true;

		return $this;
	}

	/**
	 * Fire registerd service hooks.
	 */
	public function boot_hooks() {
		// Loop through service hooks and call `init` method.
		foreach ( array_reverse( $this->service_hooks ) as $hook ) {
			$this->init_service_hooks( $hook );
		}

		$this->booted = true;
	}

	/**
	 * Call init service hooks.
	 *
	 * @param  Service_Hooks $hook Service hook instance.
	 * @return void
	 */
	protected function init_service_hooks( Service_Hooks $hook ) {
		if ( ! empty( $hook->in_admin ) && is_admin() ) {
			$hook->init( $this );
		} else {
			$hook->init( $this );
		}
	}

	/**
	 * Get the registered service hook instance if it exists.
	 *
	 * @param  string $hook Service hook class name.
	 * @return Service_Hooks|null
	 */
	public function get_registered( $hook ) {
		$name = is_string( $hook ) ? $hook : get_class( $hook );

		if ( ! array_key_exists( $name, $this->loaded_hooks ) ) {
			return;
		}

		foreach ( $this->service_hooks as $service_hook ) {
			if ( get_class( $service_hook ) === $name ) {
				return $service_hook;
			}
		}
	}

	/**
	 * If the container is booted.
	 *
	 * @return boolean
	 */
	public function is_booted() {
		return $this->booted;
	}
}
