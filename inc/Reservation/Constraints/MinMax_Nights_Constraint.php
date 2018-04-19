<?php
namespace AweBooking\Reservation\Constraints;

use AweBooking\Reservation\Request;
use AweBooking\Calendar\Finder\Response;
use AweBooking\Calendar\Finder\Constraint;

class MinMax_Nights_Constraint implements Constraint {
	/**
	 * The reservation request.
	 *
	 * @var \AweBooking\Reservation\Request
	 */
	protected $request;

	/**
	 * The resources.
	 *
	 * @var array
	 */
	protected $resources;

	protected $min_nights;
	protected $max_nights;

	/**
	 * Constructor.
	 *
	 * @param \AweBooking\Reservation\Request $request   The reservation request.
	 * @param array                           $resources Array of resources as ID.
	 */
	public function __construct( Request $request, $resources = [], $min_nights = 0, $max_nights = 0 ) {
		$this->request    = $request;
		$this->resources  = ! is_array( $resources ) ? [ $resources ] : $resources;
		$this->min_nights = $min_nights;
		$this->max_nights = $max_nights;
	}

	/**
	 * {@inheritdoc}
	 */
	public function apply( Response $response ) {
		$resources = $response->get_resources();

		$nights = $this->request->timespan->nights();

		foreach ( $response->get_included() as $resource => $include ) {
			// In case we provided a resources but not found in current loop just ignore them.
			if ( $this->resources && ! in_array( $resource, $this->resources ) ) {
				continue;
			}

			if ( $this->min_nights && $nights < $this->min_nights ) {
				$response->reject( $include['resource'], Response::CONSTRAINT, $this );
			} elseif ( $this->max_nights && $nights > $this->max_nights ) {
				$response->reject( $include['resource'], Response::CONSTRAINT, $this );
			}
		}
	}
}
