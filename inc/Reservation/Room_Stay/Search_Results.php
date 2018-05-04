<?php
namespace AweBooking\Reservation\Room_Stay;

use AweBooking\Support\Collection;
use AweBooking\Reservation\Request;

class Search_Results implements \IteratorAggregate {
	/**
	 * The reservation request.
	 *
	 * @var \AweBooking\Reservation\Request
	 */
	protected $request;

	/**
	 * The items.
	 *
	 * @var \AweBooking\Support\Collection
	 */
	protected $items;

	/**
	 * Constructor.
	 *
	 * @param \AweBooking\Reservation\Request $request The reservation request.
	 * @param array                           $items   The items.
	 */
	public function __construct( Request $request, array $items ) {
		$this->request = $request;
		$this->items   = new Collection( $items );
	}

	/**
	 * Get the search request.
	 *
	 * @return \AweBooking\Reservation\Request
	 */
	public function get_request() {
		return $this->request;
	}

	/**
	 * Determines if has any items.
	 *
	 * @return bool
	 */
	public function has_items() {
		return count( $this->items ) > 0;
	}

	/**
	 * Get search items.
	 *
	 * @return \AweBooking\Support\Collection
	 */
	public function get_items() {
		return $this->items;
	}

	/**
	 * Getter property.
	 *
	 * @param  string $key Property name.
	 * @return mixed
	 */
	public function __get( $key ) {
		return $this->{$key};
	}

	/**
	 * Get an iterator for the items.
	 *
	 * @return \ArrayIterator
	 */
	public function getIterator() {
		return $this->items->getIterator();
	}
}
