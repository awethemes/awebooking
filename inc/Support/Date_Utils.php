<?php
namespace AweBooking\Support;

use DateTime;
use Carbon\Carbon;

class Date_Utils {
	/**
	 * Return a datetime as Carbon object with time set to 00:00:00.
	 *
	 * @param  mixed $date The date format or UNIX timestamp.
	 * @return Carbon
	 */
	public static function create_date( $date ) {
		return static::create_datetime( $date )->startOfDay();
	}

	/**
	 * Create a datetime instance of Carbon.
	 *
	 * @param  mixed $datetime The datetime format or UNIX timestamp.
	 * @return Carbon
	 */
	public static function create_datetime( $datetime ) {
		// If this value is already a Carbon instance, we shall just return it as is.
		if ( $datetime instanceof Carbon ) {
			return $datetime;
		}

		// Same as DateTime object, but we'll convert to Carbon object.
		if ( $datetime instanceof DateTime ) {
			return Carbon::instance( $datetime );
		}

		// If this value is an integer, we will assume it is a UNIX timestamp's value
		// and format a Carbon object from this timestamp.
		if ( is_numeric( $datetime ) ) {
			return Carbon::createFromTimestamp( $datetime );
		}

		// If the value is in simply "Y-m-d" format, we will instantiate the
		// Carbon instances from that format. And reset the time to 00:00:00.
		if ( static::is_standard_date_format( $datetime ) ) {
			return Carbon::createFromFormat( 'Y-m-d', $datetime )->startOfDay();
		}

		// Finally, create datetime based on standard ISO-8601 date format.
		return Carbon::createFromFormat( 'Y-m-d H:i:s', $datetime );
	}

	/**
	 * Determine if the given value is a standard date format.
	 *
	 * @param  string $date A string of "Y-m-d" date format.
	 * @return bool
	 */
	public static function is_standard_date_format( $date ) {
		return preg_match( '/^(\d{4})-(\d{1,2})-(\d{1,2})$/', $date );
	}

	/**
	 * Get days in month.
	 *
	 * @param  int        $month Number of month from 1 to 12.
	 * @param  int|string $year  Month of year, default is this year.
	 * @return int
	 */
	public static function days_in_month( $month, $year = 'this year' ) {
		if ( ! is_int( $year ) ) {
			$carbon = (new Carbon( $year ))->month( $month );
		} else {
			$carbon = Carbon::createFromDate( $year, $month, 1 );
		}

		// @codingStandardsIgnoreLine
		return (int) $carbon->daysInMonth;
	}

	public static function is_validate_year( $year ) {
		try {
			$received_year = Carbon::createFromDate( $year, 1, 1 );
		} catch ( \Exception $e ) {
			return false;
		}

		$current_year  = Carbon::now()->startOfYear();

		return $received_year->between(
			$current_year->copy()->subYears( 50 ), // 50 years back.
			$current_year->copy()->addYears( 50 )  // To next 50 years.
		);
	}

	// TODO: Remove this.
	public static function get_booking_request_query( $extra_args = array() ) {
		return Utils::get_booking_request_query( $extra_args );
	}
}
