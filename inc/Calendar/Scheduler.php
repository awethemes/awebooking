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
}
