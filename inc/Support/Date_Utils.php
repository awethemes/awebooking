<?php
namespace AweBooking\Support;

use AweBooking\Calendar\Period\Day;

class Date_Utils {
	/**
	 * Get days in month.
	 *
	 * @param  int        $month Number of month from 1 to 12.
	 * @param  int|string $year  Month of year, default is this year.
	 * @return int
	 */
	public static function days_in_month( $month, $year = 'this year' ) {
		if ( ! is_int( $year ) ) {
			$carbon = ( new Carbonate( $year ) )->month( $month );
		} else {
			$carbon = Carbonate::createFromDate( $year, $month, 1 );
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
		if ( ! is_string( $date ) ) {
			return false;
		}

		return (bool) preg_match( '/^(\d{4})-(\d{1,2})-(\d{1,2})$/', $date );
	}

	/**
	 * Get classess for a date.
	 *
	 * @param  Carbonate $date       Date instance.
	 * @param  string    $base_class The base class.
	 * @return array
	 */
	public static function get_date_classes( $date, $base_class = '' ) {
		$date = $date instanceof Day
			? $date->get_start_date()
			: Carbonate::create_date( $date );

		$classes[] = $base_class;

		// Is current day is today, future or past.
		if ( $date->isToday() ) {
			$classes[] = 'today';
		} elseif ( $date->isPast() ) {
			$classes[] = 'past';
		} elseif ( $date->isFuture() ) {
			$classes[] = 'future';
		}

		if ( $date->isWeekend() ) {
			$classes[] = 'weekend';
		}

		if ( $date->isSameDay( $date->copy()->lastOfMonth() ) ) {
			$classes[] = 'lastmonth';
		}

		return array_filter( $classes );
	}

	/**
	 * Retrieve a month label.
	 *
	 * @param  string|int $month Month number from '01' through '12'.
	 * @param  string     $type  Optional, [full, abbrev].
	 * @return string
	 */
	public static function get_month_name( $month, $type = 'full' ) {
		global $wp_locale;

		$month_name = $wp_locale->get_month( $month );

		if ( 'abbrev' === $type ) {
			return $wp_locale->get_month_abbrev( $month_name );
		}

		return $month_name;
	}

	/**
	 * Retrieve a weekday label.
	 *
	 * @param  int    $weekday Weekday number, 0 for Sunday through 6 Saturday.
	 * @param  string $type    Optional, [initial, 'abbrev', 'full'].
	 * @return string
	 */
	public static function get_weekday_name( $weekday, $type = 'full' ) {
		global $wp_locale;

		$weekday_name = $wp_locale->get_weekday( $weekday );

		switch ( $type ) {
			case 'initial':
				return $wp_locale->get_weekday_initial( $weekday_name );
			case 'abbrev':
				return $wp_locale->get_weekday_abbrev( $weekday_name );
			default:
				return $weekday_name;
		}
	}
}
