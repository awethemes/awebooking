<?php

namespace AweBooking\Availability\Constraints;

use AweBooking\Model\Common\Timespan;
use AweBooking\Calendar\Finder\Response;

class Night_Stay_Constraint extends Constraint {
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
	 * Minimum nights stay.
	 *
	 * @var int
	 */
	protected $min_nights;

	/**
	 * Maximum nights stay.
	 *
	 * @var int
	 */
	protected $max_nights;

	/**
	 * Constructor.
	 *
	 * @param array|int $resources  Array of resources as ID.
	 * @param Timespan  $timespan   The timespan.
	 * @param int       $min_nights Minimum nights stay.
	 * @param int       $max_nights Maximum nights stay.
	 */
	public function __construct( $resources, Timespan $timespan, $min_nights = 0, $max_nights = 0 ) {
		$this->resources  = ! is_array( $resources ) ? [ $resources ] : $resources;
		$this->timespan   = $timespan;
		$this->min_nights = $min_nights;
		$this->max_nights = $max_nights;
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

		foreach ( $response->get_included() as $resource => $include ) {
			// In case we provided a resources but not found in current loop just ignore them.
			if ( $this->resources && ! in_array( $resource, $this->resources ) ) {
				continue;
			}

			if ( ( $this->min_nights && $timespan->nights() < $this->min_nights ) ||
				 ( $this->max_nights && $timespan->nights() > $this->max_nights ) ) {
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
		/* translators: %s Number of nights */
		$minimum_stay = $this->min_nights ? sprintf( _n( '%s night', '%s nights', $this->min_nights, 'awebooking' ), number_format_i18n( $this->min_nights ) ) : '';

		/* translators: %s Number of nights */
		$maximum_stay = $this->max_nights ? sprintf( _n( '%s night', '%s nights', $this->max_nights, 'awebooking' ), number_format( $this->max_nights ) ) : '';

		switch ( true ) {
			case ( $this->min_nights && $this->max_nights ):
				return ( $this->min_nights == $this->max_nights )
					/* translators: %s Minimum nights stay (1 day, 2 days, etc.) */
					? sprintf( esc_html__( 'The stay must be for %s', 'awebooking' ), $minimum_stay )
					/* translators: %1$s Minimum nights stay, %$2s Maximum nights stay */
					: sprintf( esc_html__( 'The stay must be at least %1$s and at most %2$s', 'awebooking' ), $minimum_stay, $maximum_stay );
			case ( $this->min_nights ):
				/* translators: %s Minimum nights stay (1 day, 2 days, etc.) */
				return sprintf( esc_html__( 'The stay must be for at least %s', 'awebooking' ), $minimum_stay );
			case ( $this->max_nights ):
				/* translators: %s Maximum nights stay (1 day, 2 days, etc.) */
				return sprintf( esc_html__( 'The stay cannot be more than %s', 'awebooking' ), $maximum_stay );
		}

		return '';
	}
}
