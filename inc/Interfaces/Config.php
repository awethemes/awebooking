<?php
namespace AweBooking\Interfaces;

interface Config {
	/**
	 * Get a config by key.
	 *
	 * @param  string $key     A string configure key.
	 * @param  mixed  $default Default value will be return if key not set,
	 *                         if null (default) pass, default setting value will be return.
	 * @return mixed
	 */
	public function get( $key, $default = null );
}
