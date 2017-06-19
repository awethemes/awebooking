<?php
namespace AweBooking\BAT\Traits;

trait Unit_Trait {
	/**
	 * Default unit value.
	 *
	 * @var int
	 */
	protected $unit_value = 0;

	/**
	 * The unit constraints.
	 *
	 * @var array
	 */
	protected $constraints = [];

	/**
	 * Get Unit ID.
	 *
	 * @return int
	 */
	public function getUnitId() {
		return $this->get_id();
	}

	/**
	 * Get Unit ID.
	 *
	 * @param int $unit_id Unit ID.
	 */
	public function setUnitId( $unit_id ) {
		$this->set_id( $unit_id );
	}

	/**
	 * Get the Unit default value.
	 *
	 * @return int
	 */
	public function getDefaultValue() {
		return (int) $this->unit_value;
	}

	/**
	 * Set Unit default value.
	 *
	 * @param int $unit_value Default Unit value.
	 */
	public function setDefaultValue( $unit_value ) {
		$this->unit_value = $unit_value;
	}

	/**
	 * Get the constraints of this unit.
	 *
	 * @param array $constraints An array of constraints.
	 */
	public function setConstraints( $constraints ) {
		$this->constraints = $constraints;
	}

	/**
	 * Get the constraints of this unit.
	 *
	 * @return array
	 */
	public function getConstraints() {
		return $this->constraints;
	}
}
