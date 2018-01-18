<?php
namespace AweBooking\ICalendar;

use AweBooking\ICalendar\Reader\Reader;
use AweBooking\ICalendar\Reader\Adapter\Remote_Adapter;
use AweBooking\ICalendar\Reader\Adapter\Stream_Adapter;
use AweBooking\ICalendar\Reader\Adapter\Contents_Adapter;
use AweBooking\ICalendar\Reader\Adapter\Adapter_Interface;

class ICalendar {
	/**
	 * Read an ICS from different source: file, resource, remote URL or plain contents or ICS.
	 *
	 * @param  mixed                  $ics     The file path, resource, URL or plain contents or ICS.
	 * @param  Adapter_Interface|null $adapter The speical adapter instead guest by $ics type.
	 * @return \AweBooking\ICalendar\Reader\Reader_Result
	 *
	 * @throws \AweBooking\ICalendar\Reader\Reading_Exception
	 */
	public static function read( $ics, Adapter_Interface $adapter = null ) {
		return static::create_reader( $ics, $adapter )->read();
	}

	/**
	 * Create an ICS reader.
	 *
	 * @param  mixed                  $ics     The file path, resource, URL or plain contents or ICS.
	 * @param  Adapter_Interface|null $adapter The speical adapter instead guest by $ics type.
	 * @return \AweBooking\ICalendar\Reader\Reader
	 *
	 * @throws \AweBooking\ICalendar\Reader\Reading_Exception
	 */
	public static function create_reader( $ics, Adapter_Interface $adapter = null ) {
		if ( is_null( $adapter ) ) {
			if ( is_string( $ics ) && true === filter_var( $ics, FILTER_VALIDATE_URL ) ) {
				$adapter = new Remote_Adapter;
			} elseif ( is_resource( $ics ) || file_exists( $ics ) ) {
				$adapter = new Stream_Adapter;
			} else {
				$adapter = new Contents_Adapter;
			}
		}

		return new Reader( $ics, $adapter );
	}
}
