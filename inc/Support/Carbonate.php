<?php
namespace AweBooking\Support;

use Carbon\Carbon;

class Carbonate extends Carbon {
	// Weekday.
	const SUNDAY    = 0;
	const MONDAY    = 1;
	const TUESDAY   = 2;
	const WEDNESDAY = 3;
	const THURSDAY  = 4;
	const FRIDAY    = 5;
	const SATURDAY  = 6;

	/**
	 * Format the instance as a readable wp date.
	 *
	 * @return string
	 */
	public function to_wp_date_string() {
		return $this->date_i18n( awebooking( 'setting' )->get_date_format() );
	}

	/**
	 * Format the instance as a readable wp time.
	 *
	 * @return string
	 */
	public function to_wp_time_string() {
		return $this->date_i18n( awebooking( 'setting' )->get_time_format() );
	}

	/**
	 * Format the instance as a readable wp date and time.
	 *
	 * @return string
	 */
	public function to_wp_datetime_string() {
		return $this->to_wp_date_string() . ' ' . $this->to_wp_time_string();
	}

	/**
	 * Format datetime use `date_i18n`.
	 *
	 * @param  string $fomrat A valid date format string.
	 * @return string
	 */
	public function date_i18n( $fomrat ) {
		return date_i18n( $fomrat, $this->getTimestamp() );
	}

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
		if ( $datetime instanceof Carbonate ) {
			return $datetime;
		}

		// Same as DateTime object, but we'll convert to Carbon object.
		if ( $datetime instanceof \DateTime ) {
			return static::instance( $datetime );
		}

		if ( $datetime instanceof \DateTimeInterface ) {
			return new static( $datetime->format( 'Y-m-d H:i:s.u' ), $datetime->getTimeZone() );
		}

		// If this value is an integer, we will assume it is a UNIX timestamp's value
		// and format a Carbon object from this timestamp.
		if ( is_numeric( $datetime ) ) {
			return static::createFromTimestamp( $datetime );
		}

		// If the value is in simply "Y-m-d" format, we will instantiate the
		// Carbon instances from that format. And reset the time to 00:00:00.
		if ( is_string( $datetime ) && static::is_standard_date_format( $datetime ) ) {
			return static::createFromFormat( 'Y-m-d', $datetime )->startOfDay();
		}

		// Finally, create datetime based on standard ISO-8601 date format.
		return static::createFromFormat( 'Y-m-d H:i:s', $datetime );
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
			$carbon = (new static( $year ))->month( $month );
		} else {
			$carbon = static::createFromDate( $year, $month, 1 );
		}

		// @codingStandardsIgnoreLine
		return (int) $carbon->daysInMonth;
	}

	/**
	 * Returns true if year is valid.
	 *
	 * We'll check if input year in range of current year -20, +30.
	 *
	 * @param  int $year Input year to validate.
	 * @return bool
	 */
	public static function is_valid_year( $year ) {
		return filter_var( $year, FILTER_VALIDATE_INT, [
			'options' => [
				'min_range' => intval( date( 'Y' ) ) - 20,
				'max_range' => intval( date( 'Y' ) ) + 30,
			],
		]);
	}

	/**
	 * Determine if the given value is a standard date format.
	 *
	 * @param  string $date A string of "Y-m-d" date format.
	 * @return bool
	 */
	public static function is_standard_date_format( $date ) {
		return (bool) preg_match( '/^(\d{4})-(\d{1,2})-(\d{1,2})$/', $date );
	}
}
