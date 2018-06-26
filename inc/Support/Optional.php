<?php

namespace AweBooking\Support;

use Illuminate\Support\Arr;

/**
 * Original from Illuminate\Support\Optional.
 *
 * @link https://github.com/illuminate/support/blob/master/Optional.php
 */
class Optional implements \ArrayAccess {
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
	 * Dynamically check a property exists on the underlying object.
	 *
	 * @param  string $name The property key name.
	 * @return bool
	 */
	public function __isset( $name ) {
		if ( is_object( $this->value ) ) {
			return isset( $this->value->{$name} );
		}

		if ( is_array( $this->value ) || $this->value instanceof \ArrayObject ) {
			return isset( $this->value[ $name ] );
		}

		return false;
	}

	/**
	 * Dynamically access a property on the underlying object.
	 *
	 * @param  string $key The getter key name.
	 * @return mixed
	 */
	public function __get( $key ) {
		if ( is_object( $this->value ) ) {
			return isset( $this->value->{$key} ) ? $this->value->{$key} : null;
		}
	}

	/**
	 * Dynamically pass a method to the underlying object.
	 *
	 * @param  string $method     Method name.
	 * @param  array  $parameters Call method parameters.
	 *
	 * @return mixed
	 */
	public function __call( $method, $parameters ) {
		if ( is_object( $this->value ) ) {
			return $this->value->{$method}( ...$parameters );
		}
	}

	/**
	 * Determine if an item exists at an offset.
	 *
	 * @param  mixed $key The offset key.
	 * @return bool
	 */
	public function offsetExists( $key ) {
		return Arr::accessible( $this->value ) && Arr::exists( $this->value, $key );
	}

	/**
	 * Get an item at a given offset.
	 *
	 * @param  mixed $key The offset key.
	 * @return mixed
	 */
	public function offsetGet( $key ) {
		return Arr::get( $this->value, $key );
	}

	/**
	 * Set the item at a given offset.
	 *
	 * @param  mixed $key   The offset key.
	 * @param  mixed $value The offset value.
	 * @return void
	 */
	public function offsetSet( $key, $value ) {
		if ( Arr::accessible( $this->value ) ) {
			$this->value[ $key ] = $value;
		}
	}

	/**
	 * Unset the item at a given offset.
	 *
	 * @param  string $key The offset key.
	 * @return void
	 */
	public function offsetUnset( $key ) {
		if ( Arr::accessible( $this->value ) ) {
			unset( $this->value[ $key ] );
		}
	}
}
