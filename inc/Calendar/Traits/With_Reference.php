<?php

namespace AweBooking\Calendar\Traits;

trait With_Reference {
	/**
	 * The scheduler reference.
	 *
	 * @var mixed
	 */
	protected $reference;

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
	 *
	 * @return $this
	 */
	public function set_reference( $reference ) {
		$this->reference = $reference;

		return $this;
	}
}
