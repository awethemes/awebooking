<?php

namespace AweBooking\Reservation\Traits;

use AweBooking\Reservation\Models\Deposit;

trait Has_Deposits {
	/**
	 * Store the deposits.
	 *
	 * @var \AweBooking\Reservation\Models\Deposit[]
	 */
	protected $deposits = [];

	/**
	 * Sets the deposit amount.
	 *
	 * @param        $amount
	 * @param string $type
	 * @param null   $from
	 */
	public function deposit( $amount, $type = Deposit::PERCENT, $from = null ) {
	}
}
