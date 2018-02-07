<?php
namespace AweBooking\Reservation\Pricing;

use AweBooking\Support\Decimal;
use AweBooking\Support\Collection;

class Breakdown extends Collection {
	/**
	 * Get total amount of the breakdown.
	 *
	 * @return \AweBooking\Support\Decimal
	 */
	public function total() {
		return $this->reduce( function ( $total, $item ) {
			return $total->add( $item->get_amount() );
		}, Decimal::zero() );
	}
}
