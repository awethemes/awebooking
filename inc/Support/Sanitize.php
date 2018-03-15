<?php
namespace AweBooking\Support;

class Sanitize {
	use Traits\Macroable;

	/**
	 * Sanitize days of week.
	 *
	 * @param  array $days The days.
	 * @return array|null
	 */
	public static function weekday( $days ) {
		// If empty days, leave and return null.
		if ( empty( $days ) ) {
			return;
		}

		$days = array_filter( array_unique( $days ) );

		return $days;
	}
}
