<?php
namespace AweBooking\Calendar;

use AweBooking\Support\Collection;

class Scheduler extends Collection {
	/**
	 * The scheduler name.
	 *
	 * @var string
	 */
	protected $name;

	/**
	 * The scheduler reference.
	 *
	 * @var mixed
	 */
	protected $reference;

	/**
	 * Get the Calendar name.
	 *
	 * @return string
	 */
	public function get_name() {
		return $this->name;
	}

	/**
	 * Set the Calendar name.
	 *
	 * @param  string $name The Calendar name.
	 * @return $this
	 */
	public function set_name( $name ) {
		$this->name = $name;

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
