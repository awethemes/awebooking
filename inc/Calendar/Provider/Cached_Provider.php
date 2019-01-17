<?php

namespace AweBooking\Calendar\Provider;

use AweBooking\Support\Carbonate;
use AweBooking\Calendar\Event\Event_Interface;
use AweBooking\Calendar\Exceptions\StoreNotSupportedException;
use AweBooking\Calendar\Provider\Contracts\Storable;

class Cached_Provider implements Provider_Interface, Contracts\Storable {
	/**
	 * The underlying event provider.
	 *
	 * @var Provider_Interface
	 */
	protected $provider;

	/**
	 * The cached events.
	 *
	 * @var array
	 */
	protected $events = [];

	/**
	 * Constructor.
	 *
	 * @param Provider_Interface $provider The provider.
	 */
	public function __construct( Provider_Interface $provider ) {
		$this->provider = $provider;
	}

	/**
	 * {@inheritdoc}
	 */
	public function store_event( Event_Interface $event ) {
		if ( ! $this->provider instanceof Storable ) {
			throw new StoreNotSupportedException( 'The provider `' . get_class( $this->provider ) . '` not support store event.' );
		}

		return $this->provider->store_event( $event );
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_events( Carbonate $start_date, Carbonate $end_date, array $options = [] ) {
		$index = $this->generate_cache_hash( $start_date, $end_date, $options );

		if ( isset( $this->events[ $index ] ) ) {
			return $this->events[ $index ];
		}

		$events = $this->provider->get_events( $start_date, $end_date, $options );
		$this->events[ $index ] = $events;

		return $events;
	}

	/**
	 * Flush the cached events.
	 *
	 * This forces the events to be fetched again from the underlying provider.
	 *
	 * @return void
	 */
	public function flush() {
		$this->events = null;
	}

	/**
	 * Generate the cache hash.
	 *
	 * @param  Carbonate $start_date The start date.
	 * @param  Carbonate $end_date   The end date.
	 * @param  array     $options    The options.
	 * @return string
	 */
	protected function generate_cache_hash( $start_date, $end_date, $options ) {
		return $start_date->getTimestamp() . '_' . $end_date->getTimestamp() . '_' . md5( serialize( $options ) );
	}
}
