<?php
namespace AweBooking\Reservation\Search;

use AweBooking\Support\Collection;

class Results extends Collection {
	/**
	 * Get only items have remain rooms.
	 *
	 * @return static
	 */
	public function only_available_items() {
		return $this->filter( function( $item ) {
			return count( $item->response_plans->get_included() ) > 0;
		});
	}
}
