<?php

namespace AweBooking\Calendar\Provider;

use AweBooking\Support\Carbonate;

class Aggregate_Provider implements Provider_Interface {
	/**
	 * The list of providers.
	 *
	 * @var array Provider_Interface[]
	 */
	private $providers;

	/**
	 * Constructor.
	 *
	 * @param array $providers Provider_Interface[].
	 *
	 * @throws \InvalidArgumentException
	 */
	public function __construct( array $providers ) {
		foreach ( $providers as $provider ) {
			if ( ! $provider instanceof Provider_Interface ) {
				throw new \InvalidArgumentException( 'Providers must implement Provider_Interface' );
			}

			$this->providers[] = $provider;
		}
	}

	/**
	 * Adds a provider.
	 *
	 * @param  Provider_Interface $provider The provider.
	 * @return $this
	 */
	public function add( Provider_Interface $provider ) {
		$this->providers[] = $provider;

		return $this;
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_events( Carbonate $start_date, Carbonate $end_date, array $options = [] ) {
		$events = [];

		foreach ( $this->providers as $provider ) {
			$events[] = $provider->get_events( $start_date, $end_date, $options );
		}

		return array_merge( ...$events );
	}
}
