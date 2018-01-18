<?php
namespace AweBooking\Reservation\Source;

use AweBooking\Model\Commission;

interface Have_Commission {
	/**
	 * Get the commission.
	 *
	 * @return \AweBooking\Model\Commission
	 */
	public function get_commission();

	/**
	 * Set the commission.
	 *
	 * @param \AweBooking\Model\Commission $commission The commission.
	 */
	public function set_commission( Commission $commission );
}
