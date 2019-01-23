<?php

namespace AweBooking\Calendar\Resource;

use AweBooking\Support\Traits\Fluent_Getter;
use AweBooking\Calendar\Traits\With_Reference;
use AweBooking\Calendar\Traits\With_Constraints;

class Resource implements Resource_Interface {
	use Fluent_Getter, With_Reference, With_Constraints;

	/**
	 * The resource ID.
	 *
	 * @var int
	 */
	protected $id;

	/**
	 * The resource value.
	 *
	 * @var int
	 */
	protected $value;

	/**
	 * The resource title.
	 *
	 * @var string
	 */
	protected $title;

	/**
	 * The resource description.
	 *
	 * @var string
	 */
	protected $description;

	/**
	 * Create new resource unit.
	 *
	 * @param int     $id    The ID.
	 * @param integer $value The value of this resource.
	 */
	public function __construct( $id, $value = 0 ) {
		$this->id    = (int) $id;
		$this->value = (int) $value;
	}

	/**
	 * Get the resource ID.
	 *
	 * @return int
	 */
	public function get_id() {
		return $this->id;
	}

	/**
	 * Set the resource ID.
	 *
	 * @param int $id The new ID.
	 *
	 * @return $this
	 */
	public function set_id( $id ) {
		$this->id = (int) $id;

		return $this;
	}

	/**
	 * Get the resource value.
	 *
	 * @return string
	 */
	public function get_value() {
		return $this->value;
	}

	/**
	 * Set the resource value.
	 *
	 * @param int $value The new value.
	 *
	 * @return $this
	 */
	public function set_value( $value ) {
		$this->value = (int) $value;

		return $this;
	}

	/**
	 * Get the resource title.
	 *
	 * @return string
	 */
	public function get_title() {
		return $this->title;
	}

	/**
	 * Set the resource title.
	 *
	 * @param int $title The new title.
	 *
	 * @return $this
	 */
	public function set_title( $title ) {
		$this->title = $title;

		return $this;
	}

	/**
	 * Get the resource description.
	 *
	 * @return string
	 */
	public function get_description() {
		return $this->description;
	}

	/**
	 * Set the resource description.
	 *
	 * @param int $description The new description.
	 *
	 * @return $this
	 */
	public function set_description( $description ) {
		$this->description = $description;

		return $this;
	}
}
