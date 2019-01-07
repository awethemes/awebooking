<?php

namespace AweBooking\Model\Booking;

class Note implements \ArrayAccess {
	/**
	 * The note ID.
	 *
	 * @var string
	 */
	public $id;

	/**
	 * The content of the note.
	 *
	 * @var string
	 */
	public $content;

	/**
	 * The date when the note was created.
	 *
	 * @var bool
	 */
	public $date_created;

	/**
	 * Name of author add this note.
	 *
	 * @var string
	 */
	public $added_by = '';

	/**
	 * Whether the this note is for customer.
	 *
	 * @var bool
	 */
	public $customer_note = false;

	/**
	 * Create a new note instance.
	 *
	 * @param array $attributes The note attributes.
	 */
	public function __construct( $attributes = [] ) {
		$this->update( $attributes );
	}

	/**
	 * Update the note attributes.
	 *
	 * @param  array $attributes The note attributes.
	 * @return $this
	 */
	public function update( $attributes = [] ) {
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
