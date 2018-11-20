<?php

use AweBooking\Support\Period;
use AweBooking\Support\Carbonate;
use AweBooking\Model\Common\Timespan;

/**
 * Create a Carbonate by given a date format.
 *
 * @param  mixed $date The date format.
 * @param  mixed $tz   The timezone string or DateTimeZone instance.
 * @return \AweBooking\Support\Carbonate
 */
function abrs_date( $date, $tz = null ) {
	return abrs_rescue( function() use ( $date, $tz ) {
		return Carbonate::create_date( $date, $tz ?: abrs_get_wp_timezone() );
	});
}

/**
 * Create a Carbonate by given a date time format.
 *
 * @param  mixed $datetime The date time format.
 * @param  mixed $tz       The timezone string or DateTimeZone instance.
 * @return \AweBooking\Support\Carbonate
 */
function abrs_date_time( $datetime, $tz = null ) {
	return abrs_rescue( function() use ( $datetime, $tz ) {
		return Carbonate::create_date_time( $datetime, $tz ?: abrs_get_wp_timezone() );
	});
}

/**
 * Retrieve the timezone string.
 *
 * Adapted from wc_timezone_string().
 *
 * @return string PHP timezone string for the site
 */
function abrs_get_wp_timezone() {
	static $timezone;

	// For increment performance.
	if ( ! empty( $timezone ) ) {
		return $timezone;
	}

	// If site timezone string exists, return it.
	if ( $timezone = get_option( 'timezone_string' ) ) {
		return $timezone;
	}

	// Get UTC offset, if it isn't set then return UTC.
	if ( 0 === ( $utc_offset = (int) get_option( 'gmt_offset', 0 ) ) ) {
		return $timezone = 'UTC';
	}

	// Adjust UTC offset from hours to seconds.
	$utc_offset *= 3600;

	// Attempt to guess the timezone string from the UTC offset.
	if ( $timezone = timezone_name_from_abbr( '', $utc_offset ) ) {
		return $timezone;
	}

	// Last try, guess timezone string manually.
	foreach ( timezone_abbreviations_list() as $abbr ) {
		foreach ( $abbr as $city ) {
			if ( $city['timezone_id'] && (int) $city['offset'] === $utc_offset && (bool) date( 'I' ) === (bool) $city['dst'] ) {
				return $timezone = $city['timezone_id'];
			}
		}
	}

	// Fallback to UTC.
	return $timezone = 'UTC';
}

/**
 * Create a timespan.
 *
 * @param  string  $start_date The start date.
 * @param  string  $end_date   The end date.
 * @param  integer $min_nights Optional, requires minimum of nights.
 * @param  boolean $strict     Optional, if true the start date must be greater than or equal to today.
 * @return \AweBooking\Model\Common\Timespan|WP_Error
 */
function abrs_timespan( $start_date, $end_date, $min_nights = 0, $strict = false ) {
	try {
		$timespan = new Timespan( $start_date, $end_date );

		if ( is_int( $min_nights ) && $min_nights > 0 ) {
			$timespan->requires_minimum_nights( $min_nights );
		}

		// Validate when strict mode.
		if ( $strict && abrs_date( $timespan->get_start_date() )->lt( abrs_date( 'today' ) ) ) {
			return new WP_Error( 'timespan_error', esc_html__( 'Specified arrival date is prior to today\'s date.', 'awebooking' ) );
		}

		return $timespan;
	} catch ( Exception $e ) {
		return new WP_Error( 'timespan_error', esc_html__( 'Please enter valid dates', 'awebooking' ) );
	}
}

/**
 * Get number of days in a month.
 *
 * @param  int        $month Number of month from 1 to 12.
 * @param  int|string $year  Month of year, default is this year.
 * @return int
 */
function abrs_days_in_month( $month, $year = 'this year' ) {
	if ( ! is_int( $year ) ) {
		$carbon = ( new Carbonate( $year ) )->month( $month );
	} else {
		$carbon = Carbonate::createFromDate( $year, $month );
	}

	// @codingStandardsIgnoreLine
	return (int) $carbon->daysInMonth;
}

/**
 * Determine if the given value is a standard date format.
 *
 * @param  string $date A string of "Y-m-d" date format.
 * @return bool
 */
function abrs_is_standard_date( $date ) {
	if ( is_string( $date ) ) {
		return (bool) preg_match( '/^(\d{4})-(\d{1,2})-(\d{1,2})$/', $date );
	}

	return false;
}

/**
 * Retrieve a month label.
 *
 * @param  string|int $month Month number from '01' through '12'.
 * @param  string     $type  Optional, [full, abbrev].
 * @return string
 */
function abrs_month_name( $month, $type = 'full' ) {
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
function abrs_weekday_name( $weekday, $type = 'full' ) {
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

/**
 * Get the week_days begin at "start_of_week".
 *
 * @param  string $day_label The day_label, "abbrev", "initial", "full".
 * @return array
 */
function abrs_days_of_week( $day_label = 'full' ) {
	global $wp_locale;

	$week_days   = [];
	$week_begins = (int) get_option( 'start_of_week' );

	for ( $i = 0; $i <= 6; $i++ ) {
		$wd = ( $i + $week_begins ) % 7;
		$wd_name = $wp_locale->get_weekday( $wd );

		if ( 'initial' === $day_label ) {
			$wd_name = $wp_locale->get_weekday_initial( $wd_name );
		} elseif ( 'abbrev' === $day_label ) {
			$wd_name = $wp_locale->get_weekday_abbrev( $wd_name );
		}

		$week_days[ $wd ] = $wd_name;
	}

	return $week_days;
}

/**
 * Get classes for a date.
 *
 * @param  mixed $date The date.
 * @return array
 */
function abrs_date_classes( $date ) {
	$date = ( $date instanceof Period )
		? $date->get_start_date()
		: abrs_date_time( $date );

	$classes = [];

	// Is current day is today, future or past.
	if ( $date->isToday() ) {
		$classes[] = 'today';
	} elseif ( $date->isPast() ) {
		$classes[] = 'past';
	} elseif ( $date->isFuture() ) {
		$classes[] = 'future';
	}

	// Weekday.
	if ( $date->isWeekend() ) {
		$classes[] = 'weekend';
	}

	// Last month.
	if ( $date->isSameDay( $date->copy()->lastOfMonth() ) ) {
		$classes[] = 'lastmonth';
	}

	return array_filter( $classes );
}

/**
 * Return a list of hours in day.
 *
 * @return string
 */
function abrs_list_hours() {
	return apply_filters( 'abrs_list_hours', [
		'0'  => '00:00 - 01:00',
		'1'  => '01:00 - 02:00',
		'2'  => '02:00 - 03:00',
		'3'  => '03:00 - 04:00',
		'4'  => '04:00 - 05:00',
		'5'  => '05:00 - 06:00',
		'6'  => '06:00 - 07:00',
		'7'  => '07:00 - 08:00',
		'8'  => '08:00 - 09:00',
		'9'  => '09:00 - 10:00',
		'10' => '10:00 - 11:00',
		'11' => '11:00 - 12:00',
		'12' => '12:00 - 13:00',
		'13' => '13:00 - 14:00',
		'14' => '14:00 - 15:00',
		'15' => '15:00 - 16:00',
		'16' => '16:00 - 17:00',
		'17' => '17:00 - 18:00',
		'18' => '18:00 - 19:00',
		'19' => '19:00 - 20:00',
		'20' => '20:00 - 21:00',
		'21' => '21:00 - 22:00',
		'22' => '22:00 - 23:00',
		'23' => '23:00 - 00:00',
	]);
}

/**
 * Localizes the flatpickr datepicker.
 *
 * @link https://flatpickr.js.org/localization/
 *
 * @global WP_Locale $wp_locale The WordPress date and time locale object.
 */
function abrs_localize_flatpickr() {
	global $wp_locale;

	if ( ! wp_script_is( 'flatpickr', 'enqueued' ) ) {
		return;
	}

	$datepicker_defaults = wp_json_encode([
		'firstDayOfWeek' => absint( get_option( 'start_of_week' ) ),
		'weekdays'       => [
			'shorthand'  => array_values( $wp_locale->weekday_abbrev ),
			'longhand'   => array_values( $wp_locale->weekday ),
		],
		'months'         => [
			'shorthand'  => array_values( $wp_locale->month_abbrev ),
			'longhand'   => array_values( $wp_locale->month ),
		],
	]);

	wp_add_inline_script( 'flatpickr', "(function() { flatpickr.localize({$datepicker_defaults}); })();" );
}
