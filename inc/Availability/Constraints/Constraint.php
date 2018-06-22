<?php

namespace AweBooking\Availability\Constraints;

use AweBooking\Calendar\Finder\Constraint as Constraint_Interface;

abstract class Constraint implements Constraint_Interface {
	/**
	 * Returns a text describing for this constraint.
	 *
	 * @return string
	 */
	public function as_string() {
		return '';
	}
}
