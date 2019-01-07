<?php

namespace AweBooking\Model\Common;

class Fee implements \ArrayAccess {
	/**
	 * The fee ID.
	 *
	 * @var string|int
	 */
	public $id;

	/**
	 * The fee name.
	 *
	 * @var string
	 */
	public $name;

	/**
	 * The fee amount.
	 *
	 * @var int|string
	 */
	public $amount;

	/**
	 * Is tax include in fee?
	 *
	 * @var bool
	 */
	public $taxable = false;

	/**
	 * The tax rate ID.
	 *
	 * @var int
	 */
	public $tax_rate = 0;

	/**
	 * Constructor.
	 *
	 * @param array $args The fee properties.
	 */
	public function __construct( $args = [] ) {
		$keys = array_keys( get_object_vars( $this ) );

		foreach ( $keys as $key ) {
			if ( isset( $args[ $key ] ) ) {
				$this->$key = $args[ $key ];
			}
		}
	}

	/**
	 * Whether the given offset exists.
	 *
	 * @param  string $offset The offset name.
	 *
	 * @return bool
	 */
	public function offsetExists( $offset ) {
		return isset( $this->{$offset} );
	}

	/**
	 * Fetch the offset.
	 *
	 * @param  string $offset The offset name.
	 *
	 * @return mixed
	 */
	public function offsetGet( $offset ) {
		return $this->{$offset};
	}

	/**
	 * Assign the offset.
	 *
	 * @param  string $offset The offset name.
	 * @param  mixed  $value  The offset value.
	 *
	 * @return void
	 */
	public function offsetSet( $offset, $value ) {
		$this->{$offset} = $value;
	}

	/**
	 * Unset the offset.
	 *
	 * @param  mixed $offset The offset name.
	 *
	 * @return void
	 */
	public function offsetUnset( $offset ) {
		// ...
	}
}
