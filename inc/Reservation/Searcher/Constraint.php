<?php
namespace AweBooking\Reservation\Searcher;

interface Constraint {
	/**
	 * Apply the constraint.
	 *
	 * @param  Availability $availability The Availability instance.
	 * @return void
	 */
	public function apply( Availability $availability );
}
