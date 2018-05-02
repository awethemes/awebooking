<?php
namespace AweBooking\Finder;

interface Constraint {
	/**
	 * Applies the constraint to a finder response.
	 *
	 * @param  \AweBooking\Finder\Response $response The finder response.
	 * @return void
	 */
	public function apply( Response $response );
}
