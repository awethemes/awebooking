<?php
namespace AweBooking\ICalendar\Reader;

use AweBooking\ICalendar\Event;
use AweBooking\ICalendar\Reader\Adapter\Adapter_Interface;
use AweBooking\Support\Carbonate;
use AweBooking\Support\Utils as U;
use Sabre\VObject\Reader as VReader;
use Sabre\VObject\Component\VCalendar;

class Reader implements Reader_Interface {
	/**
	 * The input source.
	 *
	 * URL, contents, resources, etc...
	 *
	 * @var mixed
	 */
	protected $source;

	/**
	 * The adapter implementation.
	 *
	 * @var \AweBooking\ICalendar\Reader\Adapter\Adapter_Interface
	 */
	protected $adapter;

	/**
	 * Create the reader.
	 *
	 * @param mixed             $source  The input source.
	 * @param Adapter_Interface $adapter The adapter.
	 */
	public function __construct( $source, Adapter_Interface $adapter ) {
		$this->source  = $source;
		$this->adapter = $adapter;
	}

	/**
	 * Get the source.
	 *
	 * @return mixed
	 */
	public function get_source() {
		return $this->source;
	}

	/**
	 * Set the source.
	 *
	 * @param  mixed $source The source, URL, contents, resources, etc...
	 * @return $this
	 */
	public function set_source( $source ) {
		$this->source = $source;

		return $this;
	}

	/**
	 * Get the adapter.
	 *
	 * @return Adapter_Interface
	 */
	public function get_adapter() {
		return $this->adapter;
	}

	/**
	 * Set the adapter.
	 *
	 * @param  Adapter_Interface $adapter The adapter.
	 * @return $this
	 */
	public function set_adapter( Adapter_Interface $adapter ) {
		$this->adapter = $adapter;

		return $this;
	}

	/**
	 * Read the calendar.
	 *
	 * @return Reader_Result
	 * @throws Reading_Exception
	 */
	public function read() {
		try {
			$vcalendar = VReader::read( $this->get_adapter()->get( $this->get_source() ) );
		} catch ( \Exception $e ) {
			throw new Reading_Exception( $e->getMessage() );
		}

		if ( is_null( $vcalendar ) || ! $vcalendar instanceof VCalendar || ! isset( $vcalendar->prodid ) ) {
			throw new Reading_Exception( 'Error while reading data from iCalendar.' );
		}

		$result = new Reader_Result( (string) $vcalendar->prodid, 'ical' );
		$untrusted_uid = $this->is_untrusted_uid( $vcalendar );

		// Make sure we have a list of event.
		if ( ! isset( $vcalendar->vevent ) ) {
			return $result;
		}

		foreach ( $vcalendar->vevent as $vevent ) {
			if ( 0 === strpos( (string) $vevent->summary, 'PENDING' ) ) {
				continue;
			}

			try {
				$add_event = new Event( $vevent->dtstart->getDateTime(), $vevent->dtend->getDateTime() );
			} catch ( \Exception $e ) {
				continue;
			}

			$add_event->set_uid( (string) $vevent->uid );
			$add_event->set_status( isset( $vevent->status ) ? (string) $vevent->status : '' );
			$add_event->set_summary( isset( $vevent->summary ) ? (string) $vevent->summary : '' );
			$add_event->set_description( isset( $vevent->description ) ? (string) $vevent->description : '' );

			if ( isset( $vevent->created ) ) {
				$add_event->set_created( Carbonate::create_datetime( $vevent->created->getDateTime() ) );
			}

			if ( isset( $vevent->{'LAST-MODIFIED'} ) ) {
				$add_event->set_last_modified( Carbonate::create_datetime( ( $vevent->{'LAST-MODIFIED'} )->getDateTime() ) );
			}

			if ( $untrusted_uid ) {
				$fake_uid = $add_event->get_summary() . '|' . $add_event->get_start_date()->toDateString() . '|' . $add_event->get_end_date()->toDateString();
				$add_event->set_uid( md5( $fake_uid ) );
			}

			$result->add_event( $add_event );
		}

		return $result;
	}

	/**
	 * Determines if a calendar provider give us untrusted UID of events.
	 *
	 * @see https://stackoverflow.com/questions/38193837/uid-of-airbnb-ics-will-change-every-time-i-access
	 *
	 * @param  VCalendar $vcalendar VCalendar object.
	 * @return boolean
	 */
	protected function is_untrusted_uid( VCalendar $vcalendar ) {
		$prodid = (string) $vcalendar->prodid;

		if ( 0 === strpos( $prodid, '-//Airbnb' ) || strpos( $prodid, 'TripAdvisor' ) ) {
			return true;
		}

		return true;
	}
}
