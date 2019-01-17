<?php

namespace AweBooking\Support;

use Countable;
use ArrayAccess;
use JsonSerializable;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Contracts\Support\Arrayable;

/**
 * Original from Illuminate\Support\Fluent.
 *
 * @link https://github.com/illuminate/support/blob/master/Fluent.php
 */
class Fluent implements ArrayAccess, Countable, Arrayable, Jsonable, JsonSerializable {
	/**
	 * All of the attributes set on the container.
	 *
	 * @var array
	 */
	protected $attributes = [];

	/**
	 * Create a new fluent container instance.
	 *
	 * @param  array|object $attributes The default attributes.
	 * @return void
	 */
	public function __construct( $attributes = [] ) {
		foreach ( $attributes as $key => $value ) {
			$this->set( $key, $value );
		}
	}

	/**
	 * Get an attribute from the container.
	 *
	 * @param  string $key     The key name.
	 * @param  mixed  $default The default value.
	 * @return mixed
	 */
	public function get( $key, $default = null ) {
		if ( array_key_exists( $key, $this->attributes ) ) {
			return $this->attributes[ $key ];
		}

		return value( $default );
	}

	/**
	 * Set an attribute with given value to the container.
	 *
	 * @param string $key   The key name.
	 * @param mixed  $value The value.
	 */
	public function set( $key, $value ) {
		$this->attributes[ $key ] = $value;
	}

	/**
	 * Get the attributes from the container.
	 *
	 * @return array
	 */
	public function all() {
		return $this->attributes;
	}

	/**
	 * Count the number of attributes.
	 *
	 * @return int
	 */
	public function count() {
		return count( $this->attributes );
	}

	/**
	 * Convert the Fluent instance to an array.
	 *
	 * @return array
	 */
	public function toArray() {
		return $this->all();
	}

	/**
	 * Convert the object into something JSON serializable.
	 *
	 * @return array
	 */
	public function jsonSerialize() {
		return $this->toArray();
	}

	/**
	 * Convert the Fluent instance to JSON.
	 *
	 * @param  int $options The json_encode() options.
	 * @return string
	 */
	public function toJson( $options = 0 ) {
		return json_encode( $this->jsonSerialize(), $options );
	}

	/**
	 * Determine if the given offset exists.
	 *
	 * @param  string $offset The offset key.
	 * @return bool
	 */
	public function offsetExists( $offset ) {
		return isset( $this->attributes[ $offset ] );
	}

	/**
	 * Get the value for a given offset.
	 *
	 * @param  string $offset The offset key.
	 * @return mixed
	 */
	public function offsetGet( $offset ) {
		return $this->get( $offset );
	}

	/**
	 * Set the value at the given offset.
	 *
	 * @param  string $offset The offset key.
	 * @param  mixed  $value  The offset value.
	 * @return void
	 */
	public function offsetSet( $offset, $value ) {
		$this->set( $offset, $value );
	}

	/**
	 * Unset the value at the given offset.
	 *
	 * @param  string $offset The offset key.
	 * @return void
	 */
	public function offsetUnset( $offset ) {
		unset( $this->attributes[ $offset ] );
	}

	/**
	 * Handle dynamic calls to the container to set attributes.
	 *
	 * @param  string $method     The method name.
	 * @param  array  $parameters The method parameters.
	 * @return $this
	 */
	public function __call( $method, $parameters ) {
		$value = count( $parameters ) > 0 ? $parameters[0] : true;

		$this->set( $method, $value );

		return $this;
	}

	/**
	 * Dynamically retrieve the value of an attribute.
	 *
	 * @param  string $key The key name.
	 * @return mixed
	 */
	public function __get( $key ) {
		return $this->get( $key );
	}

	/**
	 * Dynamically set the value of an attribute.
	 *
	 * @param  string $key   The key name.
	 * @param  mixed  $value The key value.
	 * @return void
	 */
	public function __set( $key, $value ) {
		$this->offsetSet( $key, $value );
	}

	/**
	 * Dynamically check if an attribute is set.
	 *
	 * @param  string $key The key name.
	 * @return bool
	 */
	public function __isset( $key ) {
		return $this->offsetExists( $key );
	}

	/**
	 * Dynamically unset an attribute.
	 *
	 * @param  string $key The key name.
	 * @return void
	 */
	public function __unset( $key ) {
		$this->offsetUnset( $key );
	}
}
