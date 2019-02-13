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
		$check_day = abrs_date( $this->timespan->get_start_date() );

		foreach ( $response->get_included() as $resource => $include ) {
			$resource = $include['resource'];

			$effective_date = $resource->get_reference()->get_effective_date();
			$expires_date   = $resource->get_reference()->get_expires_date();

			if ( $effective_date && $check_day < abrs_date( $effective_date ) ) {
				$response->reject( $resource, 'rate_effective_date', $this );
				continue;
			}

			if ( $expires_date && $check_day > abrs_date( $expires_date ) ) {
				$response->reject( $resource, 'rate_expired_date', $this );
				continue;
			}
		}
	}
}
