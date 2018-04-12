<?php
namespace AweBooking\Reservation\Searcher;

use AweBooking\Support\Collection;
use AweBooking\Reservation\Request;

class Results extends Collection {
	/**
	 * The request instance.
	 *
	 * @var \AweBooking\Reservation\Request
	 */
	protected $request;

	/**
	 * Constructor.
	 *
	 * @param \AweBooking\Reservation\Request $request The reservation request.
	 */
	public function __construct( Request $request ) {
		$this->request = $request;
	}

	/**
	 * Get back the reservation request.
	 *
	 * @return \AweBooking\Reservation\Request
	 */
	public function get_request() {
		return $this->request;
	}

	/**
	 * Get only items have remain rooms.
	 *
	 * @return static
	 */
	public function only_available_items() {
		return $this->filter( function( $item ) {
			return $item->remain_rooms()->count() > 0;
		});
	}
}
