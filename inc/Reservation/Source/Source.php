<?php
namespace AweBooking\Reservation\Source;

use AweBooking\Model\Fee;

interface Source {
	/**
	 * Get the source unique ID.
	 *
	 * @return string
	 */
	public function get_uid();

	/**
	 * Set the source unique ID.
	 *
	 * @param string $uid The source unique ID.
	 */
	public function set_uid( $uid );

	/**
	 * Get the source name.
	 *
	 * @return string
	 */
	public function get_name();

	/**
	 * Set the source name.
	 *
	 * @param string $name The source display name.
	 */
	public function set_name( $name );

	/**
	 * Get the source surcharge (tax or fee).
	 *
	 * @return Surcharge
	 */
	public function get_surcharge();

	/**
	 * Set the source surcharge.
	 *
	 * @param Surcharge $surcharge Surcharge tax or fee.
	 */
	public function set_surcharge( Fee $surcharge );
}
