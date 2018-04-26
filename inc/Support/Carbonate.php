<?php
namespace AweBooking\Support;

use Carbon\Carbon;

class Carbonate extends Carbon {
	/**
	 * Return a datetime as Carbon object with time set to 00:00:00.
	 *
	 * @param  mixed $date The date format or UNIX timestamp.
	 * @param  mixed $tz   The timezone string or DateTimeZone instance.
	 * @return Carbon
	 */
	public static function create_date( $date, $tz = null ) {
		return static::create_date_time( $date, $tz )->startOfDay();
	}

	/**
	 * Create a datetime instance of Carbon.
	 *
	 * @param  mixed $datetime The datetime format or UNIX timestamp.
	 * @param  mixed $tz       The timezone string or DateTimeZone instance.
	 * @return Carbon
	 */
	public static function create_date_time( $datetime, $tz = null ) {
		// If this value is already a Carbon instance, we shall just return it as new instance.
		if ( $datetime instanceof Carbonate ) {
			return $datetime->copy();
		}

		// Same as DateTime object, but we'll convert to Carbon object.
		if ( $datetime instanceof \DateTime ) {
			return static::instance( $datetime );
		} elseif ( $datetime instanceof \DateTimeImmutable ) {
			return new static( $datetime->format( 'Y-m-d H:i:s.u' ), $datetime->getTimeZone() );
		}

		// If this value is an integer, we will assume it is a UNIX timestamp's value
		// and format a Carbon object from this timestamp.
		if ( is_numeric( $datetime ) ) {
			return static::createFromTimestamp( $datetime, $tz );
		}

		// If the value is in simply "Y-m-d" format, we will instantiate the
		// Carbon instances from that format. And reset the time to 00:00:00.
		if ( is_string( $datetime ) && abrs_is_standard_date( $datetime ) ) {
			return static::createFromFormat( 'Y-m-d', $datetime, $tz )->startOfDay();
		} elseif ( is_string( $datetime ) && in_array( $datetime, [ 'now', 'today', 'yesterday', 'tomorrow' ] ) ) {
			return static::parse( $datetime, $tz );
		}

		// Finally, try to parse the datetime.
		return static::createFromFormat( 'Y-m-d H:i:s', $datetime, $tz );
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
}
