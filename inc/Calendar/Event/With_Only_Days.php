<?php

namespace AweBooking\Calendar\Event;

trait With_Only_Days {
	/**
	 * The specified days of week this event active.
	 *
	 * @var array|null
	 */
	protected $only_days;

	/**
	 * Set the only days of week.
	 *
	 * @param  mixed $days The input days.
	 * @return void
	 */
	public function only_days( $days ) {
		$this->only_days = abrs_sanitize_days_of_week( $days );
	}

	/**
	 * Get the only days of week.
	 *
	 * @return array|null
	 */
	public function get_only_days() {
		return $this->only_days;
	}
}
