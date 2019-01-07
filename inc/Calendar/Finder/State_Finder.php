<?php

namespace AweBooking\Calendar\Finder;

use AweBooking\Support\Period;
use AweBooking\Support\Collection;
use AweBooking\Calendar\Calendar;
use AweBooking\Calendar\Provider\Provider_Interface;

class State_Finder extends Finder {
	/* The comparison mode */
	const COMPARISON_ANY = 'any';
	const COMPARISON_ONLY = 'only';
	const COMPARISON_WITHOUT = 'without';

	/**
	 * The provider implementation.
	 *
	 * @var \AweBooking\Calendar\Provider\Provider_Interface
	 */
	protected $provider;

	/**
	 * The list of states to compare.
	 *
	 * @var array|null
	 */
	protected $states = null;

	/**
	 * The comparison mode (diff or intersect).
	 *
	 * @var string
	 */
	protected $comparison = 'diff';

	/**
	 * Constructor.
	 *
	 * @param \AweBooking\Calendar\Resource\Resources|array    $resources The resources.
	 * @param \AweBooking\Calendar\Provider\Provider_Interface $provider  The provider implementation.
	 */
	public function __construct( $resources, Provider_Interface $provider ) {
		$this->provider = $provider;

		parent::__construct( $resources );
	}

	/**
	 * Filter resources by the comparison.
	 *
	 * @param  string $comparison The comparison (any, only, without).
	 * @param  array  $states     The states.
	 *
	 * @return $this
	 */
	public function filter( $comparison = self::COMPARISON_ONLY, $states ) {
		$this->states = is_array( $states ) ? $states : [ $states ];

		$this->comparison = $comparison;

		return $this;
	}

	/**
	 * Finder resources in a period.
	 *
	 * @param  \AweBooking\Support\Period $period The period.
	 * @return \AweBooking\Calendar\Finder\Response
	 */
	public function find( Period $period ) {
		// Get all events of each resources.
		$events = $this->get_events( $period );

		// Create the response.
		$response = new Response( $period, $this->resources );

		foreach ( $events as $resource_id => $resource_events ) {
			$resource = $this->resources->get( $resource_id );

			if ( $this->states && ! $this->remaining_states( $resource_events ) ) {
				$response->add_miss( $resource, Response::INVALID_STATE );
			} else {
				$response->add_match( $resource, Response::VALID_STATE, $resource_events );
			}

			// Applly the resource constraints.
			$response->apply_constraints( $resource->get_constraints() );
		}

		// Apply the global finder constraints.
		$response->apply_constraints( $this->constraints );

		return $response;
	}

	/**
	 * Determines if valid states in events.
	 *
	 * @param  \AweBooking\Calendar\Event\Events $events The resources events.
	 * @return bool
	 */
	protected function remaining_states( $events ) {
		$current_states = $events->map( function( $e ) {
			/* @var \AweBooking\Calendar\Event\Event_Interface $e */
			return $e->get_value();
		})->all();

		if ( static::COMPARISON_ONLY === $this->comparison ) {
			return count( array_diff( $current_states, $this->states ) ) === 0;
		}

		if ( static::COMPARISON_WITHOUT === $this->comparison ) {
			return count( array_intersect( $current_states, $this->states ) ) > 0;
		}

		return count( array_intersect( $this->states, $current_states ) ) > 0;
	}

	/**
	 * Get all events in a period.
	 *
	 * @param  \AweBooking\Support\Period $period The period.
	 * @return \AweBooking\Support\Collection
	 */
	protected function get_events( Period $period ) {
		return Collection::make( $this->resources )
			->keyBy( function ( $resource ) {
				/* @var \AweBooking\Calendar\Resource\Resource_Interface $resource */
				return $resource->get_id();
			})
			->transform( function ( $resource ) use ( $period ) {
				$calendar = new Calendar( $resource, $this->provider );

				return $calendar->get_events( $period );
			});
	}
}
