<?php
namespace AweBooking\Calendar\Finder;

use AweBooking\Calendar\Calendar;
use AweBooking\Calendar\Period\Period;
use AweBooking\Calendar\Resource\Resources;
use AweBooking\Calendar\Provider\Provider_Interface;
use AweBooking\Calendar\Traits\With_Constraints;
use AweBooking\Support\Collection;

class Finder {
	use With_Constraints;

	/* The comparison mode */
	const COMPARISON_DIFF = 'diff';
	const COMPARISON_INTERSECT = 'intersect';

	/**
	 * The resources to filter.
	 *
	 * @var \AweBooking\Calendar\Resource\Resources
	 */
	protected $resources;

	/**
	 * The provider implementation.
	 *
	 * @var \AweBooking\Calendar\Provider\Provider_Interface
	 */
	protected $provider;

	/**
	 * The list of states to compare.
	 *
	 * @var array
	 */
	protected $states = [];

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
		$this->provider  = $provider;
		$this->resources = ( new Resources( $resources ) )->keyBy( 'id' );
	}

	/**
	 * Filter resources with only states.
	 *
	 * @param  array $states The states.
	 * @return $this
	 */
	public function only( $states ) {
		$this->states = is_array( $states ) ? $states : func_get_args();

		$this->comparison = static::COMPARISON_DIFF;

		return $this;
	}

	/**
	 * Filter resources without states.
	 *
	 * @param  array $states The states.
	 * @return $this
	 */
	public function without( $states ) {
		$this->states = is_array( $states ) ? $states : func_get_args();

		$this->comparison = static::COMPARISON_INTERSECT;

		return $this;
	}

	/**
	 * Using constraints.
	 *
	 * @param array $constraints The array of constraints.
	 */
	public function using( array $constraints ) {
		return $this->with_constraints( $constraints );
	}

	/**
	 * Finder resources in a period.
	 *
	 * @param  \AweBooking\Calendar\Period\Period $period The period.
	 * @return \AweBooking\Calendar\Finder\Response
	 */
	public function find( Period $period ) {
		// Get all states of each resources.
		$states = $this->get_states( $period );

		// Create the response.
		$response = new Response( $period, $this->resources );

		foreach ( $states as $resource_id => $resource_states ) {
			$resource = $this->resources->get( $resource_id );

			if ( $this->remaining_states( $resource_states ) ) {
				$response->add_match( $resource, Response::VALID_STATE );
			} else {
				$response->add_miss( $resource, Response::INVALID_STATE );
			}

			// Applly the resource constraints.
			$response->apply_constraints( $resource->get_constraints() );
		}

		// Apply the global finder constraints.
		$response->apply_constraints( $this->constraints );

		return $response;
	}

	/**
	 * Determines if still remaining states.
	 *
	 * @param  array $resource_states The resources states.
	 * @return bool
	 */
	protected function remaining_states( array $resource_states ) {
		// No states to check, leave and return true.
		if ( is_null( $this->states ) || empty( $this->states ) ) {
			return true;
		}

		if ( static::COMPARISON_DIFF === $this->comparison ) {
			$remaining = array_diff( $resource_states, $this->states );
		} else {
			$remaining = array_intersect( $resource_states, $this->states );
		}

		return ( static::COMPARISON_INTERSECT === $this->comparison )
			? count( $remaining ) > 0
			: count( $remaining ) === 0;
	}

	/**
	 * Get all resources states in a period.
	 *
	 * @param  \AweBooking\Calendar\Period\Period $period The period.
	 * @return \AweBooking\Support\Collection
	 */
	protected function get_states( Period $period ) {
		return $this->get_events( $period )->map( function( $events ) {

			return $events->transform( function( $e ) {
				return $e->get_value();
			})->all();

		});
	}

	/**
	 * Get all events in a period.
	 *
	 * @param  \AweBooking\Calendar\Period\Period $period The period.
	 * @return \AweBooking\Support\Collection
	 */
	protected function get_events( Period $period ) {
		return Collection::make( $this->resources )
			->keyBy( function ( $resource ) {
				return $resource->get_id();
			})
			->transform( function ( $resource ) use ( $period ) {
				$calendar = new Calendar( $resource, $this->provider );

				return $calendar->get_events( $period );
			});
	}
}
