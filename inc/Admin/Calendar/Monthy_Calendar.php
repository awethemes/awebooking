<?php
namespace AweBooking\Admin\Calendar;

use AweBooking\AweBooking;
use AweBooking\Factory;
use AweBooking\Hotel\Room;
use AweBooking\Hotel\Room_Type;
use AweBooking\Booking\Calendar;
use AweBooking\Support\Period;
use AweBooking\Support\Carbonate;
use Illuminate\Support\Arr;
use AweBooking\Support\Collection;
use AweBooking\Support\Abstract_Calendar;
use AweBooking\Booking\Events\Room_State;

class Monthy_Calendar extends Abstract_Calendar {
	/**
	 * ISO 8601
	 */
	const DATE_FORMAT = 'Y-m-d';

	/**
	 * The room-type instance.
	 *
	 * @var AweBooking\Hotel\Room_Type
	 */
	protected $room_type;

	/**
	 * An collection rooms of room-type.
	 *
	 * @var Collection
	 */
	protected $rooms;

	protected $room;

	/**
	 * The year we will working on.
	 *
	 * @var int
	 */
	protected $year;

	/**
	 * The month we will working on.
	 *
	 * @var int
	 */
	protected $month;

	/**
	 * The Calendar default options.
	 *
	 * @var array
	 */
	protected $defaults = [
		'date_title'       => 'l, M j, Y',
		'month_label'      => 'abbrev',  // 'abbrev', 'full'.
		'weekday_label'    => 'abbrev',  // 'initial', 'abbrev', 'full'.
		'base_class'       => 'abkngcal',
		'hide_prev_months' => true,
	];

	/**
	 * Create pricing calendar.
	 *
	 * @param Room_Type $room_type The room-type instance.
	 * @param int       $year      Year of calendar.
	 */
	public function __construct( Room_Type $room_type, $year = null, $month = null ) {
		parent::__construct();

		$this->room_type = $room_type;
		$rooms = new Collection( $this->room_type->get_rooms() );
		$this->rooms = $rooms;

		$this->year = $year ? absint( $year ) : absint( date( 'Y' ) );
		$this->month = ( $month && $month <= 12 && $month >= 1 ) ? $month : absint( date( 'n' ) );
	}

	/**
	 * Prepare setup the data.
	 *
	 * @param  mixed  $data    Mixed input data.
	 * @param  string $context Context from Calendar.
	 * @return mixed
	 */
	protected function prepare_data( $data, $context ) {
		if ( 'year' === $context && is_int( $data ) ) {
			$start_date = Carbonate::createFromDate( $data, 1, 1 );
			$end_date   = $start_date->copy()->endOfYear();
		} elseif ( $data instanceof Carbonate ) {
			$start_date = Carbonate::create_date( $data )->startOfMonth();
			$end_date   = $start_date->copy()->endOfMonth();
		} else {
			return;
		}
		$response = Factory::create_availability_calendar( $this->rooms->all() )
			->getEventsItemized( $start_date, $end_date, Calendar::BAT_DAILY );

		$resources = array_map(function( $item ) {
			return $item[ Calendar::BAT_DAY ];
		}, $response );

		return $resources;
	}

	/**
	 * Return contents of day in cell.
	 *
	 * Override this method if want custom contents.
	 *
	 * @param  Carbonate $date    Current day instance.
	 * @param  string    $context Context from Calendar.
	 * @return array
	 */
	protected function get_date_contents( Carbonate $date, $context ) {
		$room = $this->room;

		$calendar = new Calendar( [ $room ], awebooking( 'store.availability' ) );

		$current_year = Carbonate::createFromDate( $this->year, 1, 1 );
		$events = $calendar->getEvents( $current_year, $current_year->copy()->addYear() );

		$states = [];
		foreach ( $events[ $room->get_id() ] as $state ) {
			$state = Room_State::instance( $state );

			if ( $state->is_available() ) {
				continue;
			}

			$states[] = $state;
		}
		$this->state = $states;

		// Get all booking events.
		$booking_calendar = new Calendar( [ $room ], awebooking( 'store.booking' ) );
		$booking_events = $booking_calendar->getEvents( $current_year, $current_year->copy()->addYear() );

		$this->bookings = [];
		foreach ( $booking_events[ $room->get_id() ] as $event ) {
			if ( 0 === $event->getValue() ) {
				continue;
			}

			$this->bookings[] = $event;
		}

		$range = [];
		foreach ( $this->state as $state ) {
			try {
				$period = new Period( $state->getStartDate(), $state->getEndDate() );
			} catch ( \Exception $e ) {
				continue;
			}

			$period = $period->get_period();
			$_period = iterator_to_array( $period );
			$_period[] = $period->getEndDate();

			$state_class = 'unavailable';
			if ( $state->is_booked() ) {
				$state_class = 'booked';
			} else if ( $state->is_pending() ) {
				$state_class = 'pending';
			}

			foreach ( $_period as $i => $day ) {
				$carbon = Carbonate::create_date( $day );
				$next_carbon = $carbon->copy()->addDay();

				$uid = $carbon->toDateString();
				if ( ! isset( $range[ $uid ] ) ) {
					$range[ $uid ] = [
						'datetime' => $carbon,
						'classes' => [],
					];
				}

				// Get next day.
				$uid_next = $next_carbon->toDateString();
				$range[ $uid_next ] = [
					'datetime' => $next_carbon,
					'classes' => [],
					'booking' => null,
				];

				$classes = [];
				$next_classes = [];
				$booking_id = null;

				if ( $carbon->isSameDay( $state->getStartDate() ) ) {
					$classes[] = $state_class . '-start triangle-start';

					$booking = $this->get_booking( $carbon );
					if ( false !== $booking ) {
						$range[ $uid ]['booking'] = $booking;
					}
				}

				if ( $carbon->between( $state->getStartDate(), $state->getEndDate(), false ) ) {
					$classes[] = $state_class;
				}

				// Add class to next day.
				if ( $carbon->isSameDay( $state->getEndDate() ) ) {
					$next_classes[] = $state_class . '-end triangle-end';
				}

				$range[ $uid ]['classes'] = array_merge( $range[ $uid ]['classes'], $classes );
				$range[ $uid_next ]['classes'] = array_merge( $range[ $uid_next ]['classes'], $next_classes );
			}// End foreach().
		}// End foreach().

		$this->range = $range;

		$working_day = Carbonate::createFromDate( $date->year, $date->month, $date->day );
		$classes = $this->classes_for_a_day( $working_day, $working_day ); // TODO: ...

		$date_string = $working_day->toDateString();
		if ( isset( $this->range[ $date_string ] ) && ! empty( $this->range[ $date_string ]['booking'] ) ) {
			$booking = $this->range[ $date_string ]['booking'];
			$booking_id = $booking->getValue();
		}

		$contents = sprintf( '
					<div class="%1$s">
						<i>%2$s</i>
						<span class="abkngcal__day-state"></span>
						<span class="abkngcal__day-selection"></span>
					</div>',
					implode( ' ', $classes ),
					isset( $booking_id ) ? '<a href="' . esc_url( get_edit_post_link( $booking_id ) ) . '">#' . $booking_id . '</a>' : ''
				);

				unset( $booking_id );

		return $contents;
	}

	/**
	 * Display the Calendar.
	 *
	 * @return void
	 */
	public function display() {
		$date = Carbonate::createFromDate( $this->year, $this->month, 1 );

		echo '<div class="abkngcal-container abkngcal--availability-calendar">
				<div class="abkngcal-ajax-loading" style="display: none;"><div class="spinner"></div></div>';

		// @codingStandardsIgnoreStart
		echo '<h2>' . esc_html( $this->room_type->get_title() ) . '</h2>';
		$units = $this->rooms->map->only( 'id', 'name' );

		echo $this->generate_scheduler_calendar( $date, $units );
		// @codingStandardsIgnoreEnd

		echo '</div>';
		?>
			<style>
				.abkngcal--availability-calendar .abkngcal__month-heading>span {
				    width: 125px;
				    font-size: 11px;
				    text-align: left;
				    padding: 0 5px;
				    white-space: nowrap;
				    overflow: hidden;
				    text-overflow: ellipsis;
				}

				.abkngcal--availability-calendar .abkngcal__day-heading:not(.hover) {
				    background-color: #fff;
				}

				.abkngcal--availability-calendar td {
				    vertical-align: top;
				}
			</style>
		<?php
	}

	/**
	 * Setup date data before prints.
	 *
	 * @param  Carbonate $date    Date instance.
	 * @param  string    $context Context from Calendar.
	 * @return void
	 */
	protected function setup_date( Carbonate $date, $context ) {
		$this->room = $this->rooms->where( 'id', $context['id'] )->first();
	}

	/**
	 * Return row heading content for scheduler.
	 *
	 * @param  Carbonate $month Current month.
	 * @param  array     $unit  Array of current unit in loop.
	 * @return string
	 */
	protected function get_scheduler_row_heading( $month, $unit ) {
		return '<span><i class="check-column"><input type="checkbox" name="bulk-update[]" value="' . esc_attr( $unit['id'] ) . '" /></i>' . esc_html( $unit['name'] ) . '</span>';
	}


	/**
	 * Build classess for a day.
	 *
	 * @param  Carbon $working_day   Working day.
	 * @param  Carbon $working_month Working month.
	 * @return array
	 */
	public function classes_for_a_day( Carbonate $working_day, Carbonate $working_month ) {
		$classes = [];

		// Is current day is today, future or past.
		if ( $working_day->isToday() ) {
			$classes[] = 'abkngcal__day--today';
		} elseif ( $working_day->lt( $this->today ) ) {
			$classes[] = 'abkngcal__day--past';
		} elseif ( $working_day->gt( $this->today ) ) {
			$classes[] = 'abkngcal__day--future';
		}

		$date_string = $working_day->toDateString();
		if ( isset( $this->range[ $date_string ] ) ) {
			$classes[] = implode( ' ', $this->range[ $date_string ]['classes'] );
		}

		return $classes;
	}

	public function get_booking( Carbonate $day ) {
		if ( empty( $this->bookings ) ) {
			return false;
		}

		foreach ( $this->bookings as $event ) {
			if ( $event->dateIsInRange( $day ) ) {
				return $event;
			}
		}

		return false;
	}
}
