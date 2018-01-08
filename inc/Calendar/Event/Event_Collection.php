<?php
namespace AweBooking\Calendar\Event;

use AweBooking\Support\Collection;

class Event_Collection extends Collection {
	/* Constants */
	const INDEX_FORMAT = 'Y-m-d';

	/**
	 * Create a new collection.
	 *
	 * @param  mixed $items The event items.
	 * @return void
	 */
	public function __construct( $items = [] ) {
		foreach ( $items = $this->getArrayableItems( $items ) as $item ) {
			static::assert_is_event( $item );
		}

		$this->items = $items;
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

			return ! is_null( $callback ) ? $callback( $e ) : $e->get_start_date()->format( static::INDEX_FORMAT );
		});
	}

	/**
	 * {@inheritdoc}
	 */
	public function prepend( $value, $key = null ) {
		static::assert_is_event( $value );

		parent::prepend( $value, $key );
	}

	/**
	 * {@inheritdoc}
	 */
	public function offsetSet( $key, $value ) {
		static::assert_is_event( $value );

		parent::offsetSet( $key, $value );
	}

	/**
	 * Assert given value instance of Event_Interface.
	 *
	 * @param  mixed $value Input value.
	 * @return void
	 *
	 * @throws \InvalidArgumentException
	 */
	protected static function assert_is_event( $value ) {
		// Allowed nested collection when use groupBy method.
		if ( $value instanceof Event_Collection ) {
			return;
		}

		if ( ! $value instanceof Event_Interface ) {
			throw new \InvalidArgumentException( 'The resource must be instance of Event_Interface.' );
		}
	}
}
