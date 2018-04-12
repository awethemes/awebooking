<?php
namespace AweBooking\Support;

use Carbon\Carbon;

class Carbonate extends Carbon {
	/**
	 * Return a datetime as Carbon object with time set to 00:00:00.
	 *
	 * @param  mixed $date The date format or UNIX timestamp.
	 * @return Carbon
	 */
	public static function create_date( $date ) {
		return static::create_date_time( $date )->startOfDay();
	}

	/**
	 * Create a datetime instance of Carbon.
	 *
	 * @param  mixed $datetime The datetime format or UNIX timestamp.
	 * @return Carbon
	 */
	public static function create_date_time( $datetime ) {
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
			return static::createFromTimestamp( $datetime );
		}

		// If the value is in simply "Y-m-d" format, we will instantiate the
		// Carbon instances from that format. And reset the time to 00:00:00.
		if ( is_string( $datetime ) && abrs_is_standard_date( $datetime ) ) {
			return static::createFromFormat( 'Y-m-d', $datetime )->startOfDay();
		}

		// Finally, create datetime based on standard ISO-8601 date format.
		return static::createFromFormat( 'Y-m-d H:i:s', $datetime );
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
