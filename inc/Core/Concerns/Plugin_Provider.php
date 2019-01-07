<?php

namespace AweBooking\Core\Concerns;

trait Plugin_Provider {
	/**
	 * The loaded service providers.
	 *
	 * @var array
	 */
	protected $loaded_providers = [];

	/**
	 * Register a service provider.
	 *
	 * @param  \AweBooking\Support\Service_Provider|string $provider The provider class instance or class name.
	 * @param  bool                                        $force    If true, force register this provider.
	 * @return \AweBooking\Support\Service_Provider
	 */
	public function register( $provider, $force = false ) {
		if ( ! $force && ( $registered = $this->get_provider( $provider ) ) ) {
			return $registered;
		}

		// If the given "provider" is a string, we will resolve it.
		if ( is_string( $provider ) ) {
			$provider = new $provider( $this );
		}

		// Mark the given provider as registered.
		$this->loaded_providers[ get_class( $provider ) ] = $provider;

		// Call the register on the provider.
		if ( method_exists( $provider, 'register' ) ) {
			$provider->register();
		}

		// If the awebooking has already booted, we will call
		// this boot method on the provider class.
		if ( $this->is_booted() ) {
			$this->boot_provider( $provider );
		}

		return $provider;
	}

	/**
	 * Register the given provider when an action fired.
	 *
	 * @param  \AweBooking\Support\Service_Provider|string $provider The provider class instance or class name.
	 * @param  string|array                                $hook     The hook name or an array hook: [ $tag, $priority, $accepted_args ].
	 * @return void
	 *
	 * @throws \InvalidArgumentException
	 */
	public function register_when( $provider, $hook ) {
		if ( is_string( $hook ) ) {
			$hook = [ $hook, 10, 1 ];
		}

		// Given an invalid hook? let throw a exception.
		if ( ! is_array( $hook ) || 3 !== count( $hook ) ) {
			throw new \InvalidArgumentException( 'The $hook must be an array and contains three elements' );
		}

		// Extract the hook.
		list( $tag, $priority, $accepted_args ) = $hook;

		// If the action has been fired, just register the provider and leave.
		if ( did_action( $tag ) ) {
			$this->register( $provider );
			return;
		}

		add_action( $tag, function() use ( $provider ) {
			$this->register( $provider );
		}, $priority, $accepted_args );
	}

	/**
	 * Get the registered service provider instance if it exists.
	 *
	 * @param  \AweBooking\Support\Service_Provider|string $provider The service provider.
	 * @return \AweBooking\Support\Service_Provider|null
	 */
	public function get_provider( $provider ) {
		$name = is_string( $provider ) ? $provider : get_class( $provider );

		return array_key_exists( $name, $this->loaded_providers ) ? $this->loaded_providers[ $name ] : null;
	}

	/**
	 * Boot the given service provider.
	 *
	 * @param  \AweBooking\Support\Service_Provider $provider The service provider.
	 * @return void
	 */
	protected function boot_provider( $provider ) {
		if ( method_exists( $provider, 'boot' ) ) {
			$this->call( [ $provider, 'boot' ], [ $this ] );
		} elseif ( method_exists( $provider, 'init' ) ) {
			$this->call( [ $provider, 'init' ], [ $this ] );
		}
	}
}
