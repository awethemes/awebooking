<?php

namespace AweBooking\Admin\Calendar;

use Carbon\Carbon;
use AweBooking\Room_Type;
use AweBooking\Pricing\Price;
use AweBooking\BAT\Calendar;

class Pricing_Calendar {
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

	/**
	 * The room-type instance.
	 *
	 * @var AweBooking\Room_Type
	 */
	protected $room_type;

	protected $res = [];

	public function __construct( Room_Type $room_type, $year = null ) {
		$this->today     = Carbon::today();
		$this->year      = $year ? absint( $year ) : $this->today->year;
		$this->room_type = $room_type;

		$units = [];
		$units[] = $this->room_type->get_standard_rate();

		$calendar = new Calendar( $units, awebooking( 'store.pricing' ), 0 );

		$current_year = Carbon::createFromDate( $year, 1, 1 );
		$next_year    = $current_year->copy()->addYear();

		$response = $calendar->getEventsItemized( $current_year, $next_year, 'bat_daily' );
		if ( isset( $response[ $this->room_type->get_id() ]['bat_day'] ) ) {
			$this->res = $response[ $this->room_type->get_id() ]['bat_day'];
		}
	}

	public function display() {
		global $wp_locale;

		echo '<div class="abkngcal-container abkngcal-container--fullwidth" data-room-type="' . esc_attr( $this->room_type->get_id() ) . '">';

		echo '<div class="abkngcal-ajax-loading" style="display: none;"><div class="spinner"></div></div>';
		echo '<h2>' . esc_html( $this->room_type->get_title() ) . '</h2>';

		echo '<table class="abkngcal abkngcal--pricing-calendar">';
		$this->display_thead();

		echo '</tbody>';
		for ( $month = 1; $month <= 12; $month++ ) {
			$working_month = Carbon::createFromDate( $this->year, $month, 1 );

			if ( $working_month->year === $this->today->year && $working_month->month < $this->today->month ) {
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

				$working_day = Carbon::createFromDate( $this->year, $month, $day );
				$classes = $this->classes_for_a_day( $working_day, $working_month );

				printf( '
					<td class="abkngcal__day %4$s" data-month="%1$s" data-day="%2$s" data-date="%3$s" title="%5$s"><span class="abkngcal__night-selection"></span> %6$s</td>',
					esc_attr( $month ),
					esc_attr( $day ),
					esc_attr( $working_day->toDateString() ),
					implode( ' ', $classes ),
					esc_attr( $working_day->format( 'l, M j, Y' ) ),
					$this->show_price_of_night( $working_day, $working_month )
				);
			}

			echo '</tr>';
		} // End for().

		echo '</tbody></table>';
		echo '</div>';
	}

	public function show_price_of_night( Carbon $night, Carbon $month ) {
		if ( isset( $this->res[ $this->year ][ $month->month ][ 'd' . $night->day ] ) ) {
			$raw_price = $this->res[ $this->year ][ $month->month ][ 'd' . $night->day ];
			$price = Price::from_amount( $raw_price );

			$aprice = $this->room_type->get_base_price();
			if ( ! $price->equals( $aprice ) ) {
				return '<span class="abkngcal__price-change">' . $price->get_amount() . '</span>';
			}

			return '<span class="abkngcal__price">' . $price->get_amount() . '</span>';
		}
	}

	/**
	 * Build classess for a day.
	 *
	 * @param  Carbon $working_day   Working day.
	 * @param  Carbon $working_month Working month.
	 * @return array
	 */
	public function classes_for_a_day( Carbon $working_day, Carbon $working_month ) {
		$classes = [];

		// Is current day is today, future or past.
		if ( $working_day->isToday() ) {
			$classes[] = 'abkngcal__day--today';
		} elseif ( $working_day->lt( $this->today ) ) {
			$classes[] = 'abkngcal__day--past';
		} elseif ( $working_day->gt( $this->today ) ) {
			$classes[] = 'abkngcal__day--future';
		}

		return $classes;
	}

	/**
	 * Display thead of the calendar table.
	 *
	 * @return void
	 */
	protected function display_thead() {
		$days = '';
		for ( $day = 1; $day <= 31; $day++ ) {
			$days .= sprintf( '<th class="abkngcal__day-heading" data-day="%1$s"><span>%1$s</span></th>', $day );
		}

		printf( '<thead class="abkngcal__heading"><tr><th style="text-align: center;">%s</th> %s</tr></thead>', $this->year, $days );
	}
}
