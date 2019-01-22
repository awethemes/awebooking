<?php

namespace AweBooking\Calendar\Event;

use AweBooking\Support\Collection;

class Events extends Collection {
	/**
	 * Transforms events in a breakdown of days with associated values.
	 *
	 * @return \AweBooking\Calendar\Event\Itemized
	 */
	public function itemize() {
		return ( new Itemizer( $this ) )->itemize();
	}

	/**
	 * Group events by resources.
	 *
	 * @return static
	 */
	public function group() {
		return $this->groupBy( function( $e ) {
			if ( ! $e instanceof Event_Interface ) {
				throw new \LogicException( 'You should not call group method on a nested collection' );
			}

			return $e->get_resource()->get_id();
		});
	}

	/**
	 * Indexes the collection by a user-defined callback.
	 *
	 * If callback is not passed, indexed by format the start-date to "Y-m-d".
	 *
	 * @param  callable|null $callback The indexes callback.
	 * @return static
	 */
	public function indexes( callable $callback = null ) {
		return $this->groupBy( function( $e ) use ( $callback ) {
			if ( ! $e instanceof Event_Interface ) {
				throw new \LogicException( 'You should not call indexes method on a nested collection' );
			}

			return ! is_null( $callback ) ? $callback( $e ) : $e->get_start_date()->format( 'Y-m-d' );
		});
	}
}
