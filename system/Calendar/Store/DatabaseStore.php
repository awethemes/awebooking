<?php

namespace AweBooking\System\Calendar\Store;

use AweBooking\System\Database\Connection;
use AweBooking\Vendor\Illuminate\Database\Query\Builder;
use AweBooking\Vendor\Roomify\Bat\Event\Event;
use AweBooking\Vendor\Roomify\Bat\Event\EventInterface;
use AweBooking\Vendor\Roomify\Bat\Event\EventItemizer;
use AweBooking\Vendor\Roomify\Bat\Store\Store as BaseStore;
use AweBooking\Vendor\Roomify\Bat\Unit\Unit;
use DateTime;
use Throwable;

class DatabaseStore extends BaseStore
{
	// There are two types of stores - event ids and status.
	public const STORE_EVENT = 'event';
	public const STORE_STATE = 'state';

	/**
	 * The table that holds day data.
	 *
	 * @var string
	 */
	public $dayTable;

	/**
	 * The table that holds hour data.
	 *
	 * @var string
	 */
	public $hourTable;

	/**
	 * The table that holds minute data.
	 *
	 * @var string
	 */
	public $minuteTable;

	public function __construct(string $name, string $eventSource = self::STORE_STATE)
	{
		if ($eventSource === self::STORE_STATE) {
			$this->dayTable = sprintf('%s_days_states', $name);
			$this->hourTable = sprintf('%s_hours_states', $name);
			$this->minuteTable = sprintf('%s_minutes_states', $name);
		} else {
			$this->dayTable = sprintf('%s_days_events', $name);
			$this->hourTable = sprintf('%s_hours_events', $name);
			$this->minuteTable = sprintf('%s_minutes_events', $name);
		}
	}

	public function getEventData(DateTime $start_date, DateTime $end_date, $unit_ids)
	{
		$results = array_map(
			static function ($query) {
				return $query->get();
			},
			$this->buildQueries($start_date, $end_date, $unit_ids)
		);

		// @formatter:off
        $dbEvents = [];

        // Cycle through day results and setup an event array
        foreach ($results[Event::BAT_DAY] as $data) {
            // Figure out how many days the current month has.
            $days_in_month = (int) (new DateTime(sprintf('%s-%s-01', $data->year, $data->month)))
                ->format('t');

            for ($i = 1; $i <= $days_in_month; $i++) {
                $dbEvents[$data->unit_id][Event::BAT_DAY][$data->year][$data->month]['d' . $i] = $data->{'d' . $i};
            }
        }

        // With the day events taken care off let's cycle through hours
        foreach ($results[Event::BAT_HOUR] as $data) {
            for ($i = 0; $i <= 23; $i++) {
                $dbEvents[$data->unit_id][Event::BAT_HOUR][$data->year][$data->month]['d' . $data->day]['h' . $i] = $data->{'h' . $i};
            }
        }

        // With the hour events taken care off let's cycle through minutes
        foreach ($results[Event::BAT_MINUTE] as $data) {
            for ($i = 0; $i <= 23; $i++) {
                $index = sprintf('m%02d', $i);

                $dbEvents[$data->unit_id][Event::BAT_MINUTE][$data->year][$data->month]['d' . $data->day]['h' . $data->hour][$index] = $data->{$index};
            }
        }
        // @formatter:on

		return $dbEvents;
	}

	public function storeEvent(EventInterface $event, $granularity)
	{
		// Itemize an event so we can save it
		$itemized = $event->itemize(new EventItemizer($event, $granularity));

		$existingEvents = $this->getEventData(
			$event->getStartDate(),
			$event->getEndDate(),
			[$event->getUnitId()]
		);

		$unitId = $event->getUnitId();

		try {
			foreach ($itemized[Event::BAT_DAY] as $year => $months) {
				foreach ($months as $month => $days) {
					if (
						$granularity === Event::BAT_HOURLY
						&& isset($existingEvents[$unitId][EVENT::BAT_DAY][$year][$month])
					) {
						foreach ($days as $day => $value) {
							$this->itemizeSplitDay(
								$existingEvents,
								$itemized,
								$value,
								$event->getUnitId(),
								$year,
								$month,
								$day
							);
						}
					}

					$this->getConnection()->table($this->dayTable)->updateOrInsert(
						[
							'unit_id' => $event->getUnitId(),
							'year' => (int) $year,
							'month' => (int) $month,
						],
						$days
					);
				}
			}

			if ($granularity === Granularity::HOURLY && isset($itemized[Event::BAT_HOUR])) {
				foreach ($itemized[Event::BAT_HOUR] as $year => $months) {
					foreach ($months as $month => $days) {
						foreach ($days as $day => $hours) {
							// Count required as we may receive empty hours
							// for granular events that start and end on midnight.
							if (count($hours) > 0) {
								if (isset($existingEvents[$unitId][EVENT::BAT_HOUR][$year][$month][$day])) {
									foreach ($hours as $hour => $value) {
										$this->itemizeSplitHour(
											$existingEvents,
											$itemized,
											$value,
											$event->getUnitId(),
											$year,
											$month,
											$day,
											$hour
										);
									}
								}

								$this->getConnection()->table($this->hourTable)->updateOrInsert(
									[
										'unit_id' => $event->getUnitId(),
										'year' => (int) $year,
										'month' => (int) $month,
										'day' => (int) substr($day, 1),
									],
									$hours
								);
							}
						}
					}
				}

				foreach ($itemized[Event::BAT_MINUTE] as $year => $months) {
					foreach ($months as $month => $days) {
						foreach ($days as $day => $hours) {
							foreach ($hours as $hour => $minutes) {
								$this->getConnection()->table($this->minuteTable)->updateOrInsert(
									[
										'unit_id' => $event->getUnitId(),
										'year' => (int) $year,
										'month' => (int) $month,
										'day' => (int) substr($day, 1),
										'hour' => (int) substr($hour, 1),
									],
									$minutes
								);
							}
						}
					}
				}
			}

			return true;
		} catch (Throwable $e) {
			$this->getConnection()->commit();

			return false;
		}
	}

	protected function buildQueries(DateTime $startDate, DateTime $endDate, array $units): array
	{
		$queries = [];

		$queries[Event::BAT_DAY] = $this->getConnection()->table($this->dayTable);
		$queries[Event::BAT_HOUR] = $this->getConnection()->table($this->hourTable);
		$queries[Event::BAT_MINUTE] = $this->getConnection()->table($this->minuteTable);

		// Create a mock event which we will use to determine how to query the database.
		$mockEvent = new Event($startDate, $endDate, new Unit(0, 0, []), -10);

		// We don't need a granular event even if we are retrieving granular data - since we don't
		// know what the event break-down is going to be we need to get the full range of data from
		// days, hours and minutes.
		$itemized = $mockEvent->itemize(
			new EventItemizer($mockEvent, Granularity::DAILY)
		);

		// Add parameters to each query.
		$applyQuery = function (Builder $builder) use ($units, $itemized): Builder {
			$years = 0;

			foreach ($itemized[Event::BAT_DAY] as $year => $months) {
				$builder->whereNested(
					function (Builder $query) use ($months, $year) {
						$query->where('year', '=', $year);
						$query->whereIn('month', array_keys($months));
					},
					$years > 0 ? 'or' : 'and'
				);

				$years++;
			}

			if (count($units) > 0) {
				$builder->whereIn('unit_id', $units);
			}

			return $builder;
		};

		$applyQuery($queries[Event::BAT_DAY])->orderByRaw('unit_id, year, month');
		$applyQuery($queries[Event::BAT_HOUR])->orderByRaw('unit_id, year, month, day');
		$applyQuery($queries[Event::BAT_MINUTE])->orderByRaw('unit_id, year, month, day, hour');

		return $queries;
	}

	protected function getConnection(): Connection
	{
		return Connection::getInstance();
	}
}
