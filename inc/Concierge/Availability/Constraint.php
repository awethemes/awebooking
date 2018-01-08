<?php
namespace AweBooking\Concierge\Availability;

interface Constraint {
	/**
	 * Apply the constraint.
	 *
	 * @param  Availability $availability The Availability instance.
	 * @return void
	 */
	public function apply( Availability $availability );
}
