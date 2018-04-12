<?php

use AweBooking\Support\Period;
use AweBooking\Support\Carbonate;

/**
 * Create a Carbonate by given a date format.
 *
 * @param  mixed $date The date format.
 * @return \AweBooking\Support\Carbonate|null
 */
function abrs_date( $date ) {
	return abrs_rescue( function() use ( $date ) {
		return Carbonate::create_date( $date );
	});
}

/**
 * Create a Carbonate by given a date time format.
 *
 * @param  mixed $datetime The date time format.
 * @return \AweBooking\Support\Carbonate|null
 */
function abrs_date_time( $datetime ) {
	return abrs_rescue( function() use ( $datetime ) {
		return Carbonate::create_date_time( $datetime );
	});
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
function abrs_week_days( $day_label = 'full' ) {
	global $wp_locale;

	$week_days = [];
	$week_begins = (int) get_option( 'start_of_week' );

	for ( $i = 0; $i <= 6; $i++ ) {
		$wd = (int) ( $i + $week_begins ) % 7;
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
 * Get classess for a date.
 *
 * @param  mixed $date The date.
 * @return array
 */
function abrs_date_classes( $date ) {
	$date = ( $date instanceof Period )
		? $date->get_start_date()
		: Carbonate::create_date( $date );

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
	return apply_filters( 'awebooking/abrs_list_hours', [
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