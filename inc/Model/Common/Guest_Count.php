<?php

namespace AweBooking\Model\Common;

class Guest_Count {
	/**
	 * The age of qualifying.
	 *
	 * @var string|int
	 */
	protected $age;

	/**
	 * The age code (eg. adults, children, infants).
	 *
	 * @var string|int
	 */
	protected $age_code;

	/**
	 * The count.
	 *
	 * @var int
	 */
	protected $count;

	/**
	 * Constructor.
	 *
	 * @param string|int $age_code The age code.
	 * @param int        $count    The count.
	 * @param string|int $age      Optional, the of qualifying.
	 */
	public function __construct( $age_code, $count, $age = null ) {
		$this->age_code = $age_code;
		$this->count = $count;
		$this->age = $age;
	}

	/**
	 * Get the age code.
	 *
	 * @return string|int
	 */
	public function get_age_code() {
		return $this->age_code;
	}

	/**
	 * Set the age code.
	 *
	 * @param string|int $age_code The age code.
	 * @return $this
	 */
	public function set_age_code( $age_code ) {
		$this->age_code = $age_code;

		return $this;
	}

	/**
	 * Get the age.
	 *
	 * @return int
	 */
	public function get_age() {
		return $this->age;
	}

	/**
	 * Set the age.
	 *
	 * @param  string|int $age The string or number of age.
	 * @return $this
	 */
	public function set_age( $age ) {
		$this->age = $age;

		return $this;
	}

	/**
	 * Get the count.
	 *
	 * @return int
	 */
	public function get_count() {
		return $this->count;
	}

	/**
	 * Set the count.
	 *
	 * @param int $count The count.
	 * @return $this
	 */
	public function set_count( $count ) {
		$this->count = absint( $count );

		return $this;
	}
}
