<?php

namespace AweBooking\Calendar\Finder;

interface Constraint {
	/**
	 * Applies the constraint to a finder response.
	 *
	 * @param  \AweBooking\Calendar\Finder\Response $response The finder response.
	 * @return void
	 */
	public function apply( Response $response );
}
