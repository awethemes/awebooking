<?php

namespace AweBooking\Component\View;

interface Engine {
	/**
	 * Get the evaluated contents of the view.
	 *
	 * @param  string $path The relative path to view.
	 * @param  array  $data The data send to view.
	 * @return string
	 */
	public function get( $path, array $data = [] );
}
