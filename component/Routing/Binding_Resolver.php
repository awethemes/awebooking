<?php

namespace AweBooking\Component\Routing;

use Closure;
use AweBooking\Plugin;
use AweBooking\Component\Http\Exceptions\ModelNotFoundException;

class Binding_Resolver {
	/**
	 * The plugin instance.
	 *
	 * @var callable
	 */
	protected $plugin;

	/**
	 * The registered route value binders.
	 *
	 * @var array
	 */
	protected $binders = [];

	/**
	 * Constructor.
	 *
	 * @param \AweBooking\Plugin $plugin The plugin instance.
	 */
	public function __construct( Plugin $plugin ) {
		$this->plugin = $plugin;
	}

	/**
	 * Resolve bindings for route parameters.
	 *
	 * @param  array $parameters The route parameters.
	 * @return array
	 */
	public function resolve( array $parameters ) {
		foreach ( $parameters as $key => $value ) {
			if ( $callback = $this->get_binding_callback( $key ) ) {
				$parameters[ $key ] = $callback( $value );
			}
		}

		return $parameters;
	}

	/**
	 * Add a new route parameter binder.
	 *
	 * @param  string          $key    The wildcard key.
	 * @param  string|callable $binder The binder, class@method or resolver callable.
	 * @return void
	 */
	public function bind( $key, $binder ) {
		$this->binders[ str_replace( '-', '_', $key ) ] = $this->binding_for_callback( $binder );
	}

	/**
	 * Register a model binder for a wildcard.
	 *
	 * @param  string        $key      The wildcard key.
	 * @param  string        $class    The model class name.
	 * @param  \Closure|null $callback The callback when model not found.
	 * @return void
	 *
	 * @throws \AweBooking\Component\Http\Exceptions\ModelNotFoundException
	 */
	public function model( $key, $class, Closure $callback = null ) {
		$this->bind( str_replace( '-', '_', $key ), $this->binding_for_model( $class, $callback ) );
	}

	/**
	 * Get the binding callback for a given binding.
	 *
	 * @param  string $key  The binding key.
	 * @return \Closure|null
	 */
	public function get_binding_callback( $key ) {
		$key = str_replace( '-', '_', $key );

		return isset( $this->binders[ $key ] ) ? $this->binders[ $key ] : null;
	}

	/**
	 * Create a Route model binding for a given callback.
	 *
	 * @param  \Closure|string $binder The binder.
	 * @return \Closure
	 */
	protected function binding_for_callback( $binder ) {
		if ( is_string( $binder ) ) {
			return $this->create_class_binding( $binder );
		}

		return $binder;
	}

	/**
	 * Create a class based binding using the IoC container.
	 *
	 * @param  string $binding The class or class@method binding.
	 * @return \Closure
	 */
	protected function create_class_binding( $binding ) {
		return function ( $value ) use ( $binding ) {
			// If the binding has an @ sign, we will assume it's being used to delimit
			// the class name from the bind method name. This allows for bindings
			// to run multiple bind methods in a single class for convenience.
			list( $class, $method ) = ( false !== strpos( $binding, '@' ) ) ? explode( '@', $binding, 2 ) : [ $binding, 'bind' ];

			$callable = [ $this->plugin->make( $class ), $method ];

			return $callable( $value );
		};
	}

	/**
	 * Create a Route model binding for a model.
	 *
	 * @param  string        $class    The model class name.
	 * @param  \Closure|null $callback The callback when model not found.
	 * @return \Closure
	 */
	protected function binding_for_model( $class, $callback = null ) {
		return function ( $value ) use ( $class, $callback ) {
			// For model binders, we will attempt to retrieve the models using the first
			// method on the model instance. If we cannot retrieve the models we'll
			// throw a not found exception otherwise we will return the instance.
			$model = new $class( $value );

			/* @var \AweBooking\Model\Model $model */
			if ( $model->exists() ) {
				return $model;
			}

			// If a callback was supplied to the method we will call that to determine
			// what we should do when the model is not found. This just gives these
			// developer a little greater flexibility to decide what will happen.
			if ( $callback instanceof Closure ) {
				return $callback( $value );
			}

			throw (new ModelNotFoundException)->set_model( $class );
		};
	}
}
