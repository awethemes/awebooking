<?php
namespace AweBooking\Finder;

use AweBooking\Support\Period;
use AweBooking\Calendar\Resource\Resources;
use AweBooking\Calendar\Traits\With_Constraints;

abstract class Finder {
	use With_Constraints;

	/**
	 * The resources to filter.
	 *
	 * @var \AweBooking\Calendar\Resource\Resources
	 */
	protected $resources;

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
		return $this->with_constraints( $constraints );
	}

	/**
	 * Finder resources in a period.
	 *
	 * @param  \AweBooking\Support\Period $period The period.
	 * @return \AweBooking\Finder\Response
	 */
	abstract public function find( Period $period );
}
