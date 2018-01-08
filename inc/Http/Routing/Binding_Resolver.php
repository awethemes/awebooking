<?php
namespace AweBooking\Http\Routing;

use Closure;
use AweBooking\AweBooking;
use InvalidArgumentException;
use AweBooking\Model\Exceptions\Model_Not_Found_Exception;

class Binding_Resolver {
	/**
	 * The awebooking instance.
	 *
	 * @var callable
	 */
	protected $awebooking;

	/**
	 * The registered route value binders.
	 *
	 * @var array
	 */
	protected $binders = [];

	/**
	 * Constructor.
	 *
	 * @param AweBooking $awebooking The awebooking instance.
	 */
	public function __construct( AweBooking $awebooking ) {
		$this->awebooking = $awebooking;
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
				$parameters[ $key ] = call_user_func( $callback, $value );
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
	 * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
	 */
	public function model( $key, $class, Closure $callback = null ) {
		$this->bind( $key, $this->binding_for_model( $class, $callback ) );
	}

	/**
	 * Get the binding callback for a given binding.
	 *
	 * @param  string $key  The binding key.
	 * @return \Closure|null
	 */
	public function get_binding_callback( $key ) {
		$key = str_replace( '-', '_', $key );

		if ( isset( $this->binders[ $key ] ) ) {
			return $this->binders[ $key ];
		}
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

			$callable = [ $this->awebooking->make( $class ), $method ];

			return call_user_func( $callable, $value );
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
			if ( is_null( $value ) ) {
				return;
			}

			// For model binders, we will attempt to retrieve the models using the first
			// method on the model instance. If we cannot retrieve the models we'll
			// throw a not found exception otherwise we will return the instance.
			$model = new $class( $value );

			if ( $model->exists() ) {
				return $model;
			}

			// If a callback was supplied to the method we will call that to determine
			// what we should do when the model is not found. This just gives these
			// developer a little greater flexibility to decide what will happen.
			if ( $callback instanceof Closure ) {
				return call_user_func( $callback, $value );
			}

			throw (new Model_Not_Found_Exception())->set_model( $class );
		};
	}
}
