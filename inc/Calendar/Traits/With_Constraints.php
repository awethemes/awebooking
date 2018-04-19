<?php
namespace AweBooking\Calendar\Traits;

trait With_Constraints {
	/**
	 * The constraints.
	 *
	 * @var array
	 */
	protected $constraints = [];

	/**
	 * Get the constraints.
	 *
	 * @return array
	 */
	public function get_constraints() {
		return $this->constraints;
	}

	/**
	 * Set the constraints.
	 *
	 * @param array $constraints The array of constraints.
	 */
	public function with_constraints( array $constraints ) {
		$this->constraints = $constraints;

		return $this;
	}
}
