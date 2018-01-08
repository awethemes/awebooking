<?php
namespace AweBooking\Calendar\Provider;

use AweBooking\Support\Carbonate;
use AweBooking\Calendar\Event\Event_Interface;
use AweBooking\ICalendar\ICalendar;
use AweBooking\ICalendar\Reader\Reader_Interface;
use AweBooking\ICalendar\Reader\Reading_Exception;

class ICalendar_Provider implements Provider_Interface {
	/**
	 * The ICalendar reader implementation.
	 *
	 * @var \AweBooking\ICalendar\Reader\Reader_Interface
	 */
	protected $reader;

	/**
	 * Constructor.
	 *
	 * @param Reader_Interface|mixed $ics The ICalendar resource.
	 */
	public function __construct( $ics ) {
		$this->reader = $ics instanceof Reader_Interface ? $ics : ICalendar::create_reader( $ics );
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_events( Carbonate $start_date, Carbonate $end_date, array $options = [] ) {
		try {
			$result = $this->reader->read();
		} catch ( Reading_Exception $e ) {
			return [];
		}

		return $result->get_events()->all();
	}
}
