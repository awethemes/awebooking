<?php

namespace AweBooking\Availability\Constraints;

use AweBooking\Model\Common\Timespan;
use AweBooking\Calendar\Finder\Response;

class Rate_Constraint extends Constraint {
	/**
	 * The Timespan instance.
	 *
	 * @var \AweBooking\Model\Common\Timespan
	 */
	protected $timespan;

	/**
	 * Constructor.
	 *
	 * @param Timespan $timespan The timespan.
	 */
	public function __construct( Timespan $timespan ) {
		$this->timespan = $timespan;
	}

	/**
	 * {@inheritdoc}
	 */
	public function apply( Response $response ) {
		$check_in_date = abrs_date( $this->timespan->get_start_date() );
		$check_out_date = abrs_date( $this->timespan->get_end_date() );

		foreach ( $response->get_included() as $resource => $include ) {
			$resource = $include['resource'];

			$effective_date = abrs_date(
				$resource->get_reference()->get_effective_date()
			) ?: abrs_date( '1970-01-01' );

			$expires_date = abrs_date(
				$resource->get_reference()->get_expires_date()
			) ?: abrs_date( '2999-01-01' );

			if ( ! $check_in_date->between( $effective_date, $expires_date ) &&
				 ! $check_out_date->between( $effective_date, $expires_date ) ) {
				$response->reject( $resource, 'invalid_date', $this );
			}
		}
	}
}
