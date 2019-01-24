<?php
namespace AweBooking\Availability\Constraints;

use AweBooking\Model\Common\Timespan;
use AweBooking\Calendar\Finder\Response;

class Checkin_Days_Constraint extends Constraint {
	/**
	 * The Timespan instance.
	 *
	 * @var \AweBooking\Model\Common\Timespan
	 */
	protected $timespan;

	/**
	 * The resources.
	 *
	 * @var array
	 */
	protected $resources;

	/**
	 * Constructor.
	 *
	 * @param array|int $resources Array of resources as ID.
	 * @param Timespan  $timespan  The timespan.
	 */
	public function __construct( $resources, Timespan $timespan ) {
		$this->resources = ! is_array( $resources ) ? [ $resources ] : $resources;
		$this->timespan  = $timespan;
	}

	/**
	 * {@inheritdoc}
	 */
	public function apply( Response $response ) {
		$timespan = $this->timespan;

		// Outside the period, just leave.
		if ( ! $timespan->to_period()->contains( $response->get_period() ) ) {
			return;
		}

		$checkin_day = $timespan
			->to_period()
			->get_start_date()
			->format( 'w' );

		foreach ( $response->get_included() as $resource => $include ) {
			$room_type = $include['resource']
				->get_reference()
				->get( 'room_type' );

			$room_type    = abrs_get_room_type( $room_type );
			$allowed_days = (array) $room_type->get( 'availability_allowed_checkin_days' );

			if ( count( $allowed_days ) > 0 && ! in_array( $checkin_day, $allowed_days ) ) {
				$response->reject( $include['resource'], Response::CONSTRAINT, $this );
			}
		}
	}

	/**
	 * Returns a text describing for this constraint.
	 *
	 * @return string
	 */
	public function as_string() {
		return '';
	}
}
