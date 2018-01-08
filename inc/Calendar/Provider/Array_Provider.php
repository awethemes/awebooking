<?php
namespace AweBooking\Calendar\Provider;

use AweBooking\Support\Carbonate;
use AweBooking\Calendar\Event\Event_Interface;

class Array_Provider implements Provider_Interface, \IteratorAggregate, \Countable {
	/**
	 * The events array.
	 *
	 * @var array
	 */
	protected $events = [];

	/**
	 * Constructor.
	 *
	 * @param array $events The events.
	 */
	public function __construct( array $events = [] ) {
		foreach ( $events as $event ) {
			$this->add( $event );
		}
	}

	/**
	 * Adds an event to the provider.
	 *
	 * @param  Event_Interface $event The event instance.
	 * @return $this
	 */
	public function add( Event_Interface $event ) {
		$this->events[] = $event;

		return $this;
	}

	/**
	 * Returns all events.
	 *
	 * @return array Event_Interface[]
	 */
	public function all() {
		return $this->events;
	}

	/**
	 * Return the number of events.
	 *
	 * @return int
	 */
	public function count() {
		return count( $this->events );
	}

	/**
	 * Retrieve an external iterator.
	 *
	 * @return \Traversable
	 */
	public function getIterator() {
		return new \ArrayIterator( $this->events );
	}

	/**
	 * TODO: ...
	 *
	 * {@inheritdoc}
	 */
	public function get_events( Carbonate $start_date, Carbonate $end_date, array $options = [] ) {
		$events = [];

		foreach ( $this->events as $event ) {
			if (
				( $event->get_start_date() >= $start_date && $event->get_start_date() < $end_date ) ||
				( $event->get_end_date() > $start_date && $event->get_end_date() <= $end_date ) ||
				( $start_date <= $event->get_start_date() && $event->get_end_date() <= $end_date ) ||
				( $event->get_start_date() <= $start_date && $end_date <= $event->get_end_date() )
			) {
				$events[] = $event;
			}
		}

		return $events;
	}
}
