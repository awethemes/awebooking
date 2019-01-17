<?php

namespace AweBooking\Component\Http\Resolver;

use Illuminate\Contracts\Container\Container;

class Container_Resolver extends Simple_Resolver {
	/**
	 * The Container implementation.
	 *
	 * @var Container
	 */
	protected $container;

	/**
	 * Create the Illuminate Container Resolver.
	 *
	 * @param Container $container The Container implementation.
	 */
	public function __construct( Container $container ) {
		$this->container = $container;
	}

	/**
	 * Make a instance of given class.
	 *
	 * @param  string $class The class name.
	 * @return mixed
	 */
	public function make( $class ) {
		return $this->container->make( $class );
	}

	/**
	 * Call to the closure/callable action.
	 *
	 * @param  callable $action     The callable of the action.
	 * @param  array    $parameters The parameters for the action.
	 * @return mixed
	 */
	public function call( callable $action, array $parameters ) {
		return $this->container->call( $action, $parameters );
	}
}
