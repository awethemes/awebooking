<?php
namespace AweBooking\Support;

/**
 * This class is copy from Illuminate\Support\Optional.
 *
 * @link https://github.com/illuminate/support/blob/master/Optional.php
 */
class Optional {
	/**
	 * The underlying object.
	 *
	 * @var mixed
	 */
	protected $value;

	/**
	 * Create a new optional instance.
	 *
	 * @param  mixed $value The object.
	 * @return void
	 */
	public function __construct( $value ) {
		$this->value = $value;
	}

	/**
	 * Dynamically access a property on the underlying object.
	 *
	 * @param  string $key Get key.
	 * @return mixed
	 */
	public function __get( $key ) {
		if ( is_object( $this->value ) ) {
			return $this->value->{$key};
		}
	}

	/**
	 * Dynamically pass a method to the underlying object.
	 *
	 * @param  string $method     Method name.
	 * @param  array  $parameters Call method parameters.
	 * @return mixed
	 */
	public function __call( $method, $parameters ) {
		if ( is_object( $this->value ) ) {
			return $this->value->{$method}(...$parameters);
		}
	}
}
