<?php

namespace Skeleton\Container;

use Closure;
use Pimple\ServiceProviderInterface;
use Pimple\Container as Pimple_Container;

class Container extends Pimple_Container {
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
	 * Binding a value/closure to the container.
	 *
	 * @param string $id    Unique identifier for the parameter or object.
	 * @param mixed  $value The value of the parameter or a closure to define an object.
	 */
	public function bind( $id, $value ) {
		$this[ $id ] = $value;
	}

	/**
	 * Resolve a object/property and call it if needed.
	 *
	 * @param  string $id Param ID.
	 * @return mixed
	 */
	public function make( $id ) {
		$factory = $this[ $id ];
		return ( $factory instanceof Closure ) ? $factory( $this ) : $factory;
	}

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

		// Binding this container into the hook.
		if ( is_null( $hook->container ) ) {
			$hook->container = $this;
		}

		// Register the hook.
		$hook->register( $this );
		foreach ( $values as $key => $value ) {
			$this[ $key ] = $value;
		}

		if ( $this->booted ) {
			$this->init_service_hooks( $hook );
		}

		// Mark the given hook as registered.
		$this->service_hooks[] = $hook;
		$this->loaded_hooks[ $class_name ] = true;

		return $this;
	}

	/**
	 * If the container is booted.
	 *
	 * @return boolean
	 */
	public function is_booted() {
		return $this->booted;
	}

	/**
	 * Fire registerd service hooks.
	 */
	public function boot() {
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
	 * Registers a service provider.
	 *
	 * @deprecated Use Container::trigger instead of.
	 *
	 * @param ServiceProviderInterface $provider A ServiceProviderInterface instance.
	 * @param array                    $values   An array of values that customizes the provider.
	 */
	public function register( ServiceProviderInterface $provider, array $values = array() ) {}
}
