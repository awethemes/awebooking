<?php
namespace AweBooking\Interfaces;

interface Pipeline {
	/**
	 * Add a new pipeline.
	 *
	 * @param  mixed $pipe //.
	 * @return $this
	 */
	public function pipe( $pipe );
}
