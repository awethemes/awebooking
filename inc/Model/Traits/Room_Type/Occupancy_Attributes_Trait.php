<?php
namespace AweBooking\Model\Traits\Room_Type;

trait Occupancy_Attributes_Trait {
	/**
	 * Get the maximum occupancy.
	 *
	 * @return int
	 */
	public function get_maximum_occupancy() {
		return apply_filters( $this->prefix( 'get_maximum_occupancy' ), $this['maximum_occupancy'], $this );
	}

	/**
	 * Set the maximum occupancy.
	 *
	 * @param  int $value The number value.
	 * @return $this
	 */
	public function set_maximum_occupancy( $value ) {
		$this->attributes['maximum_occupancy'] = max( absint( $value ), 1 );

		return $this;
	}

	/**
	 * Get number adults allowed.
	 *
	 * @return int
	 */
	public function get_number_adults() {
		return apply_filters( $this->prefix( 'get_number_adults' ), $this['number_adults'], $this );
	}

	/**
	 * Set the number adults.
	 *
	 * @param  int $number_adults The number value.
	 * @return $this
	 */
	public function set_number_adults( $number_adults ) {
		$this->attributes['number_adults'] = $this->fillter_occupancy_number( $number_adults );

		return $this;
	}

	/**
	 * Get number children allowed.
	 *
	 * @return int
	 */
	public function get_number_children() {
		return apply_filters( $this->prefix( 'get_number_children' ), $this['number_children'], $this );
	}

	/**
	 * Set the number children.
	 *
	 * @param  int $number_children The number value.
	 * @return $this
	 */
	public function set_number_children( $number_children ) {
		$this->attributes['number_children'] = $this->fillter_occupancy_number( $number_children );

		return $this;
	}

	/**
	 * Get number children allowed.
	 *
	 * @return int
	 */
	public function get_number_infants() {
		return apply_filters( $this->prefix( 'get_number_infants' ), $this['number_infants'], $this );
	}

	/**
	 * Set the number infants.
	 *
	 * @param  int $number_infants The number value.
	 * @return $this
	 */
	public function set_number_infants( $number_infants ) {
		$this->attributes['number_infants'] = $this->fillter_occupancy_number( $number_infants );

		return $this;
	}

	/**
	 * Determines is this room_type include infants in max calculations?
	 *
	 * @return boolean
	 */
	public function is_calculation_infants() {
		return apply_filters( $this->prefix( 'is_calculation_infants' ), $this['calculation_infants'], $this );
	}

	/**
	 * Filter the number occupancy value.
	 *
	 * @param  mixed $value Input data.
	 * @return int
	 */
	protected function fillter_occupancy_number( $value ) {
		if ( ! is_numeric( $value ) ) {
			return 1;
		}

		return min( absint( $value ), (int) $this->get_maximum_occupancy() );
	}
}
