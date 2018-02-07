<?php
namespace AweBooking\Reservation\Searcher;

use AweBooking\Support\Collection;

class Results extends Collection {
	/**
	 * Get only items have remain rooms.
	 *
	 * @return static
	 */
	public function only_available_items() {
		return $this->filter( function( $item ) {
			return $item->remain_rooms()->count() > 0;
		} );
	}
}
