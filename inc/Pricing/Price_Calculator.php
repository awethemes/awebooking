<?php
namespace AweBooking\Pricing;

use RuntimeException;
use InvalidArgumentException;
use AweBooking\Interfaces\Pipeline;
use AweBooking\Interfaces\Price as Price_Interface;

class Price_Calculator implements Pipeline {
	/**
	 * The price being passed through the pipeline.
	 *
	 * @var Price
	 */
	protected $price;

	/**
	 * The array of class pipes.
	 *
	 * @var array
	 */
	protected $pipes = array();

	/**
	 * Create a new class instance.
	 *
	 * @param Price $price Base price.
	 */
	public function __construct( Price $price ) {
		$this->price = clone $price;
	}

	/**
	 * Set the array of pipes.
	 *
	 * @param  array|mixed $pipes An array of pipes.
	 * @return $this
	 */
	public function through( $pipes ) {
		$pipes = is_array( $pipes ) ? $pipes : func_get_args();

		$this->pipes = array();

		foreach ( $pipes as $pipe ) {
			$this->pipes[] = $pipe;
		}

		return $this;
	}

	/**
	 * Add a new pipeline to pipes.
	 *
	 * @param callable|Price_Calculator_Handle| $pipe Pipe callable or Calculator_Handle object.
	 * @return $this
	 *
	 * @throws InvalidArgumentException If given wrong parameter.
	 */
	public function pipe( $pipe ) {
		if ( is_callable( $pipe ) || $pipe instanceof Calculator_Handle ) {
			$this->pipes[] = $pipe;

			return $this;
		}

		throw new InvalidArgumentException( 'A Calculator_Handle class or callable given.' );
	}

	/**
	 * Process the pipeline.
	 *
	 * @return Price
	 * @throws RuntimeException
	 */
	public function process() {
		$price = $this->price;

		foreach ( $this->pipes as $pipe ) {
			$price = is_callable( $pipe ) ? $pipe ( $price ) : $pipe->handle( $price );

			if ( ! $price instanceof Price_Interface ) {
				throw new RuntimeException( 'Price handler must be return a Price.' );
			}
		}

		return $price;
	}
}
