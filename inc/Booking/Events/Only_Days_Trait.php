<?php
namespace AweBooking\Booking\Events;

trait Only_Days_Trait {
	/**
	 * Set only days.
	 *
	 * @var array
	 */
	protected $only_days = [];

	public function get_only_days() {
		return $this->only_days; // Ex: [0, 1, 3, 4, 5, 6].
	}

	public function set_only_days( $days ) {
		$this->only_days = $days;
	}
}
