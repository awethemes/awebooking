<?php

namespace AweBooking\Calendar\Provider\Stores;

use DateTime;
use Roomify\Bat\Unit\Unit;
use Roomify\Bat\Store\Store;
use Roomify\Bat\Event\Event;
use Roomify\Bat\Event\EventItemizer;
use Roomify\Bat\Event\EventInterface;
use AweBooking\Support\Carbonate;

class BATStore extends Store {
	/**
	 * The table that holds data.
	 *
	 * @var string
	 */
	protected $table;

	/**
	 * The table foreign key.
	 *
	 * @var string
	 */
	protected $foreign_key;

	/**
	 * Constructor.
	 *
	 * @param string $table       The table name.
	 * @param string $foreign_key The table foreign key.
	 */
	public function __construct( $table, $foreign_key ) {
		$this->table = $table;
		$this->foreign_key = $foreign_key;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getEventData( DateTime $start_date, DateTime $end_date, $unit_ids ) {
		global $wpdb;

		// @codingStandardsIgnoreLine
		$results = $wpdb->get_results( $this->buildSelectQuery( $start_date, $end_date, $unit_ids ), ARRAY_A );

		$db_events = [];

		// Cycle through day results and setup an event array.
		foreach ( $results as $data ) {
			$days_in_month = abrs_days_in_month( $data['month'], $data['year'] );

			for ( $i = 1; $i <= $days_in_month; $i++ ) {
				$db_events[ $data[ $this->foreign_key ] ][ Event::BAT_DAY ][ $data['year'] ][ $data['month'] ][ 'd' . $i ] = $data[ 'd' . $i ];
			}
		}

		return $db_events;
	}

	/**
	 * Build select query.
	 *
	 * @param  DateTime $start_date The start date.
	 * @param  DateTime $end_date   The end date.
	 * @param  array    $unit_ids   Optional, unit IDs.
	 * @return string
	 *
	 * @throws \LogicException
	 */
	public function buildSelectQuery( DateTime $start_date, DateTime $end_date, array $unit_ids = [] ) {
		global $wpdb;

		// Prepare unit_ids, remove duplicate and invalid ids.
		$unit_ids = array_unique( array_filter( $unit_ids ) );

		// Ending datepoint must be greater or equal to the starting datepoint.
		if ( $start_date > $end_date ) {
			throw new \LogicException( 'The ending datepoint must be greater or equal to the starting datepoint.' );
		}

		// Start the query builder.
		$query = 'SELECT * FROM `' . $wpdb->prefix . $this->table . '` WHERE ';

		// Create a mock event which we will use to determine how to query the database.
		$mock_event = new Event( $start_date, $end_date, new Unit( 0, 0, [] ), -10 );
		$itemized = $mock_event->itemize( new EventItemizer( $mock_event, Event::BAT_DAILY ) );

		$year_count = 0;
		$parameters = '';

		foreach ( $itemized[ Event::BAT_DAY ] as $year => $months ) {
			// If we are dealing with multiple years so add an OR.
			if ( $year_count > 0 ) {
				$parameters .= 'OR ';
			}

			$parameters .= '`year` IN (' . $year . ') ';
			$parameters .= 'AND `month` IN (' . implode( ',', array_keys( $months ) ) . ') ';

			// If unit ids are defined so add this as a filter.
			if ( count( $unit_ids ) > 0 ) {
				$parameters .= 'AND `' . $this->foreign_key . '` IN (' . implode( ',', $unit_ids ) . ') ';
			}

			$year_count++;
		}

		return $query . $parameters . 'ORDER BY `' . $this->foreign_key . '`, `year`, `month`';
	}

	/**
	 * {@inheritdoc}
	 */
	public function storeEvent( EventInterface $event, $only_days ) {
		global $wpdb;

		// We modify the $granularity functional to $only_days.
		// Seem this OK for now, but in future we need consider this.
		if ( ! is_null( $only_days ) ) {
			$only_days = abrs_sanitize_days_of_week( $only_days );
		}

		// Get existing event data from db.
		$existing_events = $this->getEventData( $event->getStartDate(), $event->getEndDate(), [ $event->getUnitId() ] );

		// Itemize an event so we can save it.
		/* @var \Roomify\Bat\Event\AbstractEvent $event */
		$itemized = $event->itemize( new EventItemizer( $event, Event::BAT_DAILY ) );

		$stored = true;

		foreach ( $itemized[ Event::BAT_DAY ] as $year => $months ) {
			foreach ( $months as $month => $days ) {
				// Parse only days in week can be perform update or insert.
				if ( ! empty( $only_days ) ) {
					$days = $this->parseOnlyDays( $only_days, $days, $month, $year );
				}

				if ( isset( $existing_events[ $event->getUnitId() ][ EVENT::BAT_DAY ][ $year ][ $month ] ) ) {
					$stored = $wpdb->update( $wpdb->prefix . $this->table, $days, [
						'year'  => $year,
						'month' => $month,
						$this->foreign_key => $event->getUnitId(),
					]);
				} else {
					$stored = $wpdb->insert( $wpdb->prefix . $this->table, array_merge( $days, [
						'year'  => $year,
						'month' => $month,
						$this->foreign_key => $event->getUnitId(),
					] ) );
				}
			}
		}

		return false !== $stored;
	}

	/**
	 * Parse only days in week can be perform action.
	 *
	 * @param  array $only_days The days of week.
	 * @param  array $days      The days in $month.
	 * @param  int   $month     The month of $days.
	 * @param  int   $year      The year of $month.
	 * @return array
	 */
	protected function parseOnlyDays( $only_days, $days, $month, $year ) {
		$return = [];

		foreach ( $days as $tday => $value ) {
			$date = Carbonate::createFromDate( $year, $month, substr( $tday, 1 ), abrs_get_wp_timezone() );

			if ( in_array( $date->dayOfWeek, $only_days ) ) { // @codingStandardsIgnoreLine
				$return[ $tday ] = $value;
			}
		}

		return $return;
	}
}
