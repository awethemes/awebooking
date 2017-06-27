<?php
namespace AweBooking\BAT;

use DateTime;
use Roomify\Bat\Event\Event;
use Roomify\Bat\Unit\UnitInterface;
use Roomify\Bat\Store\StoreInterface;
use Roomify\Bat\Calendar\Calendar as BatCalendar;

class Calendar extends BatCalendar {
	/**
	 * Calendar constructor.
	 *
	 * @param array          $units   An array of units.
	 * @param StoreInterface $store   Bat StoreInterface.
	 * @param integer        $default Default unit value.
	 */
	public function __construct( array $units, StoreInterface $store, $default = 0 ) {
		$this->store = $store;
		$this->default_value = $default;

		$this->set_units( $units );
	}

	/**
	 * Set units.
	 *
	 * @param array $units An array of UnitInterface units.
	 *
	 * @throws \InvalidArgumentException
	 */
	public function set_units( array $units ) {
		$set_units = array();

		// Loop thougth units and validate each unit.
		foreach ( $units as $unit ) {
			if ( ! $unit instanceof UnitInterface ) {
				throw new \InvalidArgumentException( __CLASS__ . '::set_units() only accepts an array of UnitInterface.' );
			}

			$set_units[] = $unit;
		}

		// Everything is ok, set the units.
		$this->units = $set_units;
	}

	/**
	 * Given a start and end time will retrieve events from the defined store.
	 *
	 * If unit_ids where defined it will filter for those unit ids.
	 *
	 * @param  DateTime $start_date //.
	 * @param  DateTime $end_date   //.
	 * @param  boolean  $reset      //.
	 * @return array
	 */
	public function getEvents( DateTime $start_date, DateTime $end_date, $reset = true ) {
		if ( $reset || empty( $this->itemized_events ) ) {
			// We first get events in the itemized format.
			$this->itemized_events = $this->getEventsItemized( $start_date, $end_date, Event::BAT_DAILY );
		}

		// We then normalize those events to create Events that get added to an array.
		return $this->getEventsNormalized( $start_date, $end_date, $this->itemized_events );
	}
}
