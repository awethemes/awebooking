<?php

namespace AweBooking\Availability\Deprecated;

use AweBooking\Model\Common\Timespan;
use AweBooking\Model\Common\Guest_Counts;

trait Deprecated {
	/**
	 * Alias of "get_los" method.
	 *
	 * @return int
	 */
	public function get_nights() {
		return $this->get_los();
	}

	/**
	 * Sets the check_in and check_out parameters by timespan.
	 *
	 * @param  \AweBooking\Model\Common\Timespan $timespan The timespan instance.
	 * @param  bool                              $lock     Lock parameters?.
	 * @return $this
	 */
	public function set_timespan( Timespan $timespan, $lock = false ) {
		$timespan->requires_minimum_nights( 1 );

		$this->set_parameter( 'check_in', $timespan->get_start_date() );
		$this->set_parameter( 'check_out', $timespan->get_end_date() );

		if ( $lock ) {
			$this->lock( 'check_in', 'check_out' );
		}

		return $this;
	}

	/**
	 * Sets the guest count.
	 *
	 * @param  string $age_code The guest age code.
	 * @param  int    $count    The count.
	 * @return $this
	 */
	public function set_guest_count( $age_code, $count = 0 ) {
		return $this;
	}

	/**
	 * Sets the Guest_Counts.
	 *
	 * @param  \AweBooking\Model\Common\Guest_Counts $guest_counts The guest_counts.
	 * @return $this
	 */
	public function set_guest_counts( Guest_Counts $guest_counts ) {
		return $this;
	}

	protected function initialize_from_objects( &$parameters ) {
		if ( isset( $parameters['timespan'] ) && $parameters['timespan'] instanceof Timespan ) {
			$timespan = $parameters['timespan'];

			$this->set_parameter( 'check_in', $timespan->get_start_date() );
			$this->set_parameter( 'check_out', $timespan->get_end_date() );

			unset( $parameters['timespan'] );
		}

		if ( isset( $parameters['guest_counts'] ) && $parameters['guest_counts'] instanceof Guest_Counts ) {
			/* @var $guests Guest_Counts */
			$guests = $parameters['guest_counts'];

			$this->set_adults( $guests->get_adults()->get_count() );
			$this->set_children( $guests->get_children() ? $guests->get_children()->get_count() : 0 );
			$this->set_infants( $guests->get_infants() ? $guests->get_infants()->get_count() : 0 );

			unset( $parameters['guest_counts'] );
		}
	}
}
