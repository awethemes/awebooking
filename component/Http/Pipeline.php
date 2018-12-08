<?php

namespace AweBooking\Component\Http;

use Closure;
use AweBooking\Component\Http\Resolver\Resolver;

class Pipeline {
	/**
	 * The resolver implementation.
	 *
	 * @var Resolver
	 */
	protected $resolver;

	/**
	 * The object being passed through the pipeline.
	 *
	 * @var mixed
	 */
	protected $passable;

	/**
	 * The array of class pipes.
	 *
	 * @var array
	 */
	protected $pipes = [];

	/**
	 * The method to call on each pipe.
	 *
	 * @var string
	 */
	protected $method = 'handle';

	/**
	 * Create a new class instance.
	 *
	 * @param  Resolver|null $resolver The resolver implementation.
	 * @return void
	 */
	public function __construct( Resolver $resolver = null ) {
		$this->resolver = $resolver;
	}

	/**
	 * Set the object being sent through the pipeline.
	 *
	 * @param  mixed $passable The object.
	 * @return $this
	 */
	public function send( $passable ) {
		$this->passable = $passable;

		return $this;
	}

	/**
	 * Set the array of pipes.
	 *
	 * @param  array|mixed $pipes The pipes.
	 * @return $this
	 */
	public function through( $pipes ) {
		$this->pipes = is_array( $pipes ) ? $pipes : func_get_args();

		return $this;
	}

	/**
	 * Set the method to call on the pipes.
	 *
	 * @param  string $method The call method.
	 * @return $this
	 */
	public function via( $method ) {
		$this->method = $method;

		return $this;
	}

	/**
	 * Run the pipeline with a final destination callback.
	 *
	 * @param \Closure $destination The destination.
	 * @return mixed
	 */
	public function then( Closure $destination ) {
		$pipeline = array_reduce(
			array_reverse( $this->pipes ), $this->carry(), $this->prepare_destination( $destination )
		);

		return $pipeline( $this->passable );
	}

	/**
	 * Get the final piece of the Closure onion.
	 *
	 * @param  \Closure $destination The destination.
	 * @return \Closure
	 */
	protected function prepare_destination( Closure $destination ) {
		return function ( $passable ) use ( $destination ) {
			return $destination( $passable );
		};
	}

	/**
	 * Get a Closure that represents a slice of the application onion.
	 *
	 * @return \Closure
	 */
	protected function carry() {
		return function ( $stack, $pipe ) {
			return function ( $passable ) use ( $stack, $pipe ) {
				$slice = $this->prepare_carry();

				$callable = $slice( $stack, $pipe );

				return $callable( $passable );
			};
		};
	}

	/**
	 * Prepare a Closure that represents a slice of the application onion.
	 *
	 * @return \Closure
	 */
	protected function prepare_carry() {
		return function ( $stack, $pipe ) {
			return function ( $passable ) use ( $stack, $pipe ) {
				if ( is_callable( $pipe ) ) {
					// If the pipe is an instance of a Closure, we will just call it directly but
					// otherwise we'll resolve the pipes out of the container and call it with
					// the appropriate method and arguments, returning the results back out.
					return $pipe( $passable, $stack );
				}

				if ( ! is_object( $pipe ) ) {
					list( $name, $parameters ) = $this->parse_pipe_string( $pipe );

					// If the pipe is a string we will parse the string and resolve the class out
					// of the dependency injection container. We can then build a callable and
					// execute the pipe function giving in the parameters that are required.
					$pipe = $this->get_resolver()->make( $name );

					$parameters = array_merge( [ $passable, $stack ], $parameters );
				} else {
					// If the pipe is already an object we'll just make a callable and pass it to
					// the pipe as-is. There is no need to do any extra parsing and formatting
					// since the object we're given was already a fully instantiated object.
					$parameters = [ $passable, $stack ];
				}

				$response = method_exists( $pipe, $this->method )
					? $pipe->{$this->method}( ...$parameters )
					: $pipe( ...$parameters );

				return $response;
			};
		};
	}

	/**
	 * Parse full pipe string to get name and parameters.
	 *
	 * @param  string $pipe The pipe string.
	 * @return array
	 */
	protected function parse_pipe_string( $pipe ) {
		list( $name, $parameters ) = array_pad( explode( ':', $pipe, 2 ), 2, [] );

		if ( is_string( $parameters ) ) {
			$parameters = explode( ',', $parameters );
		}

		return [ $name, $parameters ];
	}

	/**
	 * Get the container instance.
	 *
	 * @return Resolver
	 * @throws \RuntimeException
	 */
	protected function get_resolver() {
		if ( ! $this->resolver ) {
			throw new \RuntimeException( 'A resolver instance has not been passed to the Pipeline.' );
		}

		return $this->resolver;
	}
}
