<?php

namespace AweBooking\Calendar\Finder;

use AweBooking\Support\Period;
use AweBooking\Calendar\Resource\Resources;
use AweBooking\Calendar\Traits\With_Constraints;

class Finder {
	use With_Constraints;

	/**
	 * The resources to filter.
	 *
	 * @var \AweBooking\Calendar\Resource\Resources
	 */
	protected $resources;

	/**
	 * The filter callback.
	 *
	 * @var callable
	 */
	protected $callback;

	/**
	 * Constructor.
	 *
	 * @param \AweBooking\Calendar\Resource\Resources|array $resources The resources.
	 */
	public function __construct( $resources ) {
		$this->resources = ( new Resources( $resources ) )->keyBy( 'id' );
	}

	/**
	 * Using constraints.
	 *
	 * @param array $constraints The array of constraints.
	 */
	public function using( array $constraints ) {
		return $this->set_constraints( $constraints );
	}

	/**
	 * Sets the filter callback.
	 *
	 * @param  callable $callback The filter callback.
	 * @return $this
	 */
	public function callback( $callback ) {
		$this->callback = $callback;

		return $this;
	}

	/**
	 * Finder resources in a period.
	 *
	 * @param  \AweBooking\Support\Period $period The period.
	 * @return \AweBooking\Calendar\Finder\Response
	 */
	public function find( Period $period ) {
		$response = new Response( $period, $this->resources );

		foreach ( $this->resources as $resource ) {
			if ( is_callable( $this->callback ) ) {
				call_user_func( $this->callback, $resource, $response );
			} else {
				$response->add_match( $resource, '' );
			}

			// Applly the resource constraints.
			$response->apply_constraints( $resource->get_constraints() );
		}

		// Apply the global finder constraints.
		$response->apply_constraints( $this->constraints );

		return $response;
	}
}
