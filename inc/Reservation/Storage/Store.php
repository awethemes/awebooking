<?php

namespace AweBooking\Reservation\Storage;

interface Store {
	/**
	 * Retrieve the saved state for a reservation instance.
	 *
	 * @param  string $id The reservation ID.
	 * @return mixed
	 */
	public function get( $id );

	/**
	 * Save the state for a reservation instance.
	 *
	 * @param string $id   The reservation ID.
	 * @param mixed  $data The data store.
	 */
	public function put( $id, $data );

	/**
	 * Flush the saved state for a reservation instance.
	 *
	 * @param string $id The reservation ID.
	 */
	public function flush( $id );
}
