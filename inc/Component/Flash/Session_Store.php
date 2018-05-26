<?php
namespace AweBooking\Component\Flash;

interface Session_Store {
	/**
	 * Flash a message to the session.
	 *
	 * @param string $name The session name.
	 * @param mixed  $data The session data.
	 */
	public function flash( $name, $data );

	/**
	 * Get a message from the session.
	 *
	 * @param  string $name    Session key name.
	 * @param  mixed  $default Default value.
	 * @return mixed
	 */
	public function get_flash( $name, $default = null );
}
