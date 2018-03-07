<?php
namespace AweBooking\Support;

trait Fluent_Container {
	/**
	 * An array of the types that have been resolved.
	 *
	 * @var array
	 */
	protected $resolved = [];

	/**
	 * {@inheritdoc}
	 */
	public function get( $key, $default = null ) {
		return array_key_exists( $key, $this->attributes )
			? $this->resolve_value( $key )
			: value( $default );
	}

	/**
	 * {@inheritdoc}
	 */
	public function set( $key, $value ) {
		$this->offsetUnset( $key );

		if ( ! $value instanceof \Closure ) {
			$value = $this->wrap_closure( $value );
		}

		$this->attributes[ $key ] = $value;
	}

	/**
	 * {@inheritdoc}
	 */
	public function offsetUnset( $offset ) {
		parent::offsetUnset( $offset );

		unset( $this->resolved[ $offset ] );
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_attributes() {
		$attributes = [];

		foreach ( array_keys( $this->attributes ) as $key ) {
			$attributes[ $key ] = $this->get( $key );
		}

		return $attributes;
	}

	/**
	 * Get the Closure to be used when building a type.
	 *
	 * @param  mixed $value The value.
	 * @return \Closure
	 */
	protected function wrap_closure( $value ) {
		return function ( $context ) use ( $value ) {
			return $value;
		};
	}

	/**
	 * Resolve a value from key.
	 *
	 * @param  string $key The key name.
	 * @return mixed
	 */
	protected function resolve_value( $key ) {
		if ( array_key_exists( $key, $this->resolved ) ) {
			return $this->resolved[ $key ];
		}

		$value = call_user_func( $this->attributes[ $key ], $this );

		$this->resolved[ $key ] = $value;

		return $value;
	}
}
