<?php
namespace AweBooking\Calendar\Resource;

class Resource implements Resource_Interface {
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
	 * The resource reference (Rate, Room. etc...).
	 *
	 * @var mixed
	 */
	protected $reference;

	/**
	 * Create new resource unit.
	 *
	 * @param int     $id    The ID.
	 * @param integer $value The value of this resource.
	 */
	public function __construct( $id, $value = 0 ) {
		$this->id = (int) $id;
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
	 */
	public function set_description( $description ) {
		$this->description = $description;

		return $this;
	}

	/**
	 * Get the reference.
	 *
	 * @return mixed
	 */
	public function get_reference() {
		return $this->reference;
	}

	/**
	 * Set the reference.
	 *
	 * @param mixed $reference The reference.
	 */
	public function set_reference( $reference ) {
		$this->reference = $reference;

		return $this;
	}
}
