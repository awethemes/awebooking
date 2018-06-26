<?php
namespace AweBooking\Component\Flash;

class Message implements \ArrayAccess {
	/**
	 * The title of the message.
	 *
	 * @var string
	 */
	public $title;

	/**
	 * The body of the message.
	 *
	 * @var string
	 */
	public $message;

	/**
	 * The message level.
	 *
	 * @var string
	 */
	public $level = 'info';

	/**
	 * Whether the message should auto-hide.
	 *
	 * @var bool
	 */
	public $important = false;

	/**
	 * Whether the message is an overlay.
	 *
	 * @var bool
	 */
	public $overlay = false;

	/**
	 * Create a new message instance.
	 *
	 * @param array $attributes The message attributes.
	 */
	public function __construct( $attributes = [] ) {
		$this->update( $attributes );
	}

	/**
	 * Update the attributes.
	 *
	 * @param  array $attributes The message attributes.
	 * @return $this
	 */
	public function update( $attributes = [] ) {
		$attributes = array_filter( $attributes );

		foreach ( $attributes as $key => $attribute ) {
			$this->$key = $attribute;
		}

		return $this;
	}

	/**
	 * Whether the given offset exists.
	 *
	 * @param  string $offset The offset name.
	 * @return bool
	 */
	public function offsetExists( $offset ) {
		return isset( $this->$offset );
	}

	/**
	 * Fetch the offset.
	 *
	 * @param  string $offset The offset name.
	 * @return mixed
	 */
	public function offsetGet( $offset ) {
		return $this->$offset;
	}

	/**
	 * Assign the offset.
	 *
	 * @param  string $offset The offset name.
	 * @param  mixed  $value  The offset value.
	 * @return void
	 */
	public function offsetSet( $offset, $value ) {
		$this->$offset = $value;
	}

	/**
	 * Unset the offset.
	 *
	 * @param  mixed $offset The offset name.
	 * @return void
	 */
	public function offsetUnset( $offset ) {
		// ...
	}
}
