<?php

namespace AweBooking\Availability;

class Query_Results implements \Countable, \IteratorAggregate {
	/**
	 * The reservation request.
	 *
	 * @var \AweBooking\Availability\Request
	 */
	public $request;

	/**
	 * The items.
	 *
	 * @var \AweBooking\Support\Collection
	 */
	public $items;

	/**
	 * Constructor.
	 *
	 * @param Request $request The reservation request.
	 * @param array   $items   The items.
	 */
	public function __construct( Request $request, array $items ) {
		$this->request = $request;
		$this->items   = abrs_collect( $items );
	}

	/**
	 * Get the search request.
	 *
	 * @return \AweBooking\Availability\Request
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
	 * Get an iterator for the items.
	 *
	 * @return \ArrayIterator
	 */
	public function getIterator() {
		return $this->items->getIterator();
	}

	/**
	 * Count the number of items.
	 *
	 * @return int
	 */
	public function count() {
		return $this->items->count();
	}

	/**
	 * Getter property.
	 *
	 * @param  string $key Property name.
	 *
	 * @return mixed
	 */
	public function __get( $key ) {
		return $this->{$key};
	}
}
