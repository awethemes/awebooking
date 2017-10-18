<?php
namespace AweBooking\Admin\Calendar;

use AweBooking\AweBooking;
use AweBooking\Hotel\Room;
use AweBooking\Booking\Events\Room_State;
use AweBooking\Booking\Calendar;
use AweBooking\Support\Period;
use AweBooking\Support\Carbonate;

class Yearly_Calendar {
	/**
	 * ISO 8601
	 */
	const DATE_FORMAT = 'Y-m-d';

	/**
	 * The year we will working on.
	 *
	 * @var int
	 */
	protected $year;

	/**
	 * Current time.
	 *
	 * @var \Carbon\Carbon
	 */
	protected $today;

	protected $state;
	protected $room;

	/**
	 * //
	 *
	 * @param int|null $year The year for the calendar.
	 */
	public function __construct( $room, $year = null ) {
		$this->today  = Carbonate::today();
		$this->year = $year ? absint( $year ) : $this->today->year;

		if ( ! $room instanceof Room ) {
			$room = new Room( $room );
		}

		$this->room = $room;
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

	public function display() {
		global $wp_locale;

		$room_type = $this->room->get_room_type();

		?><div class="abkngcal-container" data-room="<?php echo esc_attr( $this->room->get_id() ) ?>">
			<div class="abkngcal-ajax-loading" style="display: none;"><div class="spinner"></div></div>

		<h2><span class="check-column"><input type="checkbox" name="bulk-update[]" value="%s" /></span> <?php echo $this->room->get_name(); ?> ( <?php echo esc_html( $room_type->get_title() ) ?> )</h2>

		<table class="abkngcal abkngcal--yearly">

		<?php $this->display_thead(); ?>

		<?php
		echo '<tbody>';

		for ( $month = 1; $month <= 12; $month++ ) {
			$working_month = Carbonate::createFromDate( $this->year, $month, 1 );

			if ( $working_month->year === $this->today->year &&
				$working_month->month < $this->today->month ) {
				continue;
			}

			echo '<tr>';

			printf(
				'<th class="abkngcal__month-heading" data-month="%2$s"><span>%1$s</span></th>',
				$wp_locale->get_month_abbrev( $wp_locale->get_month( $month ) ),
				esc_attr( $month )
			);

			for ( $day = 1; $day <= 31; $day++ ) {
				// @codingStandardsIgnoreLine
				if ( $day > $working_month->daysInMonth ) {
					echo '<td class="abkngcal__day--disabled"></td>';
					continue;
				}

				$working_day = Carbonate::createFromDate( $this->year, $month, $day );
				$classes = $this->classes_for_a_day( $working_day, $working_month );

				$date_string = $working_day->toDateString();
				if ( isset( $this->range[ $date_string ] ) && ! empty( $this->range[ $date_string ]['booking'] ) ) {
					$booking = $this->range[ $date_string ]['booking'];
					$booking_id = $booking->getValue();
				}

				printf( '
					<td class="abkngcal__day %4$s" data-month="%1$s" data-day="%2$s" data-date="%3$s" title="%5$s">
						<i>%6$s</i>
						<span class="abkngcal__day-state"></span>
						<span class="abkngcal__day-selection"></span>
					</td>',
					esc_attr( $month ),
					esc_attr( $day ),
					esc_attr( $working_day->format( static::DATE_FORMAT ) ),
					implode( ' ', $classes ),
					esc_attr( $working_day->format( 'l, M j, Y' ) ),
					isset( $booking_id ) ? '<a href="' . esc_url( get_edit_post_link( $booking_id ) ) . '">#' . $booking_id . '</a>' : ''
				);

				unset( $booking_id );
			}

			echo '</tr>';
		}// End for().

		echo '</tbody></table>';
		echo '</div>';
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

	/**
	 * Display thead of the calendar table.
	 *
	 * @return void
	 */
	protected function display_thead() {
		$year = $this->year;
		$years = [ $year -1, $year, $year + 1 ];

		$select_year = '<select>';

		foreach ( $years as $year ) {
			$selected = '';

			if ( $year === $this->year ) {
				$selected = 'selected';
			}

			$select_year .= '<option ' . $selected . ' value="' . $year . '">' . $year . '</option>';
		}

		$select_year .= '</select>';

		$select_year = '<th>' . $select_year . '</th>';
		$days = '';

		for ( $day = 1; $day <= 31; $day++ ) {
			$days .= sprintf( '<th class="abkngcal__day-heading" data-day="%1$s"><span>%1$s</span></th>', $day );
		}

		printf( '<thead class="abkngcal__heading"><tr>%s %s</tr></thead>', $select_year, $days );
	}
}
