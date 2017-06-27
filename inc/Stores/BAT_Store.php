<?php
namespace AweBooking\Stores;

use DateTime;
use Carbon\Carbon;
use Roomify\Bat\Unit\Unit;
use Roomify\Bat\Event\Event;
use Roomify\Bat\Event\EventItemizer;
use Roomify\Bat\Store\Store as Base_Store;
use AweBooking\Support\Date_Utils;

class BAT_Store extends Base_Store {
	/**
	 * The table that holds day data.
	 *
	 * @var string
	 */
	protected $table;

	/**
	 * The foreign key.
	 *
	 * @var string
	 */
	protected $foreign_key;

	/**
	 * BAT Store constructor.
	 *
	 * @param string $table       The table name.
	 * @param string $foreign_key The foreign key.
	 */
	public function __construct( $table, $foreign_key ) {
		$this->table = $table;
		$this->foreign_key = $foreign_key;
	}

	/**
	 * //
	 *
	 * @param DateTime $start_date //.
	 * @param DateTime $end_date   //.
	 * @param array    $unit_ids   //.
	 * @return array
	 */
	public function getEventData( DateTime $start_date, DateTime $end_date, $unit_ids ) {
		global $wpdb;

		$results = $wpdb->get_results(
			// @codingStandardsIgnoreLine
			$this->buildQuery( $start_date, $end_date, $unit_ids ),
			ARRAY_A
		);

		$db_events = array();

		// Cycle through day results and setup an event array.
		foreach ( $results as $data ) {
			$days_in_month = Date_Utils::days_in_month( $data['month'], $data['year'] );

			for ( $i = 1; $i <= $days_in_month; $i++ ) {
				$db_events[ $data[ $this->foreign_key ] ][ Event::BAT_DAY ][ $data['year'] ][ $data['month'] ][ 'd' . $i ] = $data[ 'd' . $i ];
			}
		}

		return $db_events;
	}

	/**
	 * //
	 *
	 * @param Event  $event      Event instance.
	 * @param string $deprecated Not used, deprecated argument.
	 * @return boolean
	 */
	public function storeEvent( Event $event, $deprecated = null ) {
		global $wpdb;

		$stored = true;

		// Get existing event data from db.
		$existing_events = $this->getEventData(
			$event->getStartDate(),
			$event->getEndDate(),
			array( $event->getUnitId() )
		);

		// Itemize an event so we can save it.
		$itemized = $event->itemize( new EventItemizer( $event, Event::BAT_DAILY ) );

		// TODO: ...
		$only_days = null;
		if ( method_exists( $event, 'get_only_days' ) ) {
			$only_days = $event->get_only_days();
		}

		foreach ( $itemized[ Event::BAT_DAY ] as $year => $months ) {
			foreach ( $months as $month => $days ) {
				// TODO: ....
				if ( ! is_null( $only_days ) ) {
					$days = $this->parse_valid_days( $days, $month, $year, $only_days );
				}

				if ( empty( $days ) ) {
					continue;
				}
				// END TODO: ...

				if ( isset( $existing_events[ $event->getUnitId() ][ EVENT::BAT_DAY ][ $year ][ $month ] ) ) {
					$stored = $wpdb->update( $wpdb->prefix . $this->table, $days, array(
						$this->foreign_key => $event->getUnitId(),
						'year'  => $year,
						'month' => $month,
					));
				} else {
					$stored = $wpdb->insert( $wpdb->prefix . $this->table, array_merge( $days, array(
						$this->foreign_key => $event->getUnitId(),
						'year'  => $year,
						'month' => $month,
					) ) );
				}
			}
		}

		return false !== $stored;
	}

	protected function parse_valid_days( $days, $month, $year, $only_days ) {
		$valid_days = [];

		if ( empty( $only_days ) ) {
			return $days;
		}

		global $wp_locale;

		$only_days = array_map( function( $i ) use ( $wp_locale ) {
			return $wp_locale->get_weekday( $i );
		}, $only_days );

		foreach ( $days as $tday => $value ) {
			$day = Carbon::createFromDate( $year, $month, substr( $tday, 1 ) );
			if ( ! in_array( $day->format( 'l' ), $only_days ) ) {
				continue;
			}

			$valid_days[ $tday ] = $value;
		}

		return $valid_days;
	}

	/**
	 * //
	 *
	 * @param DateTime $start_date //.
	 * @param DateTime $end_date   //.
	 * @param array    $unit_ids   //.
	 * @return array
	 */
	public function buildQuery( DateTime $start_date, DateTime $end_date, array $unit_ids ) {
		global $wpdb;

		$query = 'SELECT * FROM `' . $wpdb->prefix . $this->table . '` WHERE ';

		// Create a mock event which we will use to determine how to query the database.
		$mock_event = new Event( $start_date, $end_date, new Unit( 0, 0, null ), -10 );

		// We don't need a granular event even if we are retrieving granular data - since we don't
		// know what the event break-down is going to be we need to get the full range of data from
		// days, hours and minutes.
		$itemized = $mock_event->itemize( new EventItemizer( $mock_event, Event::BAT_DAILY ) );

		$year_count = 0;
		$parameters = '';

		foreach ( $itemized[ Event::BAT_DAY ] as $year => $months ) {
			if ( $year_count > 0 ) {
				// We are dealing with multiple years so add an OR.
				$parameters .= ' OR ';
			}

			$parameters .= '`year` IN (' . $year . ') ';
			$parameters .= 'AND `month` IN (' . implode( ',', array_keys( $months ) ) . ') ';

			if ( count( $unit_ids ) > 0 ) {
				// Unit ids are defined so add this as a filter.
				$parameters .= 'AND `' . $this->foreign_key . '` IN (' . implode( ',' , $unit_ids ) . ') ';
			}

			$year_count++;
		}

		// Build final query.
		$query .= $parameters . 'ORDER BY `' . $this->foreign_key . '`, `year`, `month`';

		return $query;
	}
}
