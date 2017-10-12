<?php

namespace AweBooking\Admin\Calendar;

use AweBooking\Factory;
use AweBooking\Pricing\Rate;
use AweBooking\Pricing\Price;
use AweBooking\Hotel\Room_Type;
use AweBooking\Booking\Calendar;
use AweBooking\Support\Carbonate;
use AweBooking\Support\Collection;
use Illuminate\Support\Arr;

class Pricing_Calendar {
	/**
	 * The year we will working on.
	 *
	 * @var int
	 */
	protected $year;
	protected $working_month;

	/**
	 * The room-type instance.
	 *
	 * @var AweBooking\Hotel\Room_Type
	 */
	protected $room_type;

	/**
	 * An collection rates of room-type.
	 *
	 * @var Collection
	 */
	protected $rates;

	/**
	 * Price resources by days.
	 *
	 * @var array
	 */
	protected $resources;

	public function __construct( Room_Type $room_type, $year = null ) {
		$this->year = $year ? absint( $year ) : absint( date( 'Y' ) );
		$this->working_month = Carbonate::createFromDate( $this->year, absint( date( 'm' ) ), 1 );

		$this->room_type = $room_type;
		$this->rates     = $room_type->get_rates();
		$this->resources = $this->get_price_tables();
	}

	public function display() {
		global $wp_locale;

		echo '<div class="abkngcal-container abkngcal-container--fullwidth">';
		echo '<div class="abkngcal-ajax-loading" style="display: none;"><div class="spinner"></div></div>';

		$checkbox = sprintf( '<span class="check-column"><input type="checkbox" name="bulk-update[]" value="%s" /></span>', esc_attr( $this->room_type->get_id() ) );
		echo '<h2>' . $checkbox . esc_html( $this->room_type->get_title() ) . '</h2>';

		echo '<table class="abkngcal abkngcal--pricing-calendar">';
		$this->display_thead();

		echo '<tbody>';
		foreach ( $this->rates as $rate ) {
			echo '<tr data-unit="' . esc_attr( $rate->get_id() ) . '">';
			printf( '<th class="abkngcal__month-heading" data-month="%2$d"><span>%1$s</span></th>', esc_html( $rate->get_name() ), esc_attr( $this->working_month->month ) );

			for ( $day = 1; $day <= 31; $day++ ) {
				// @codingStandardsIgnoreLine
				if ( $day > $this->working_month->daysInMonth ) {
					echo '<td class="abkngcal__day--disabled"></td>';
					continue;
				}

				$date = $this->working_month->copy()->day( $day );

				printf( '
					<td class="abkngcal__day %4$s" data-month="%1$s" data-day="%2$s" data-date="%3$s" title="%5$s"><span class="abkngcal__night-selection"></span> %6$s</td>',
					esc_attr( $date->month ),
					esc_attr( $date->day ),
					esc_attr( $date->toDateString() ),
					implode( ' ', $this->classes_for_a_day( $date ) ),
					esc_attr( $date->format( 'l, M j, Y' ) ),
					$this->show_price_of_night( $rate, $date )
				);
			}

			echo '</tr>';
		} // End foreach().

		echo '</tbody></table>';
		echo '</div>';
	}

	public function show_price_of_night( Rate $rate, Carbonate $date ) {
		$getdata = $rate->get_id() . '.' . $date->format('Y.n.\dj');

		if ( Arr::has( $this->resources, $getdata ) ) {
			$price = Price::from_integer( Arr::get( $this->resources, $getdata ) );
			$rate_price = $rate->get_base_price();

			if ( ! $price->equals( $rate_price ) ) {
				return '<span class="abkngcal__price-change">' . $price->get_amount() . '</span>';
			}

			return '<span class="abkngcal__price">' . $price->get_amount() . '</span>';
		}
	}

	protected function get_price_tables() {
		$start_date = Carbonate::createFromDate( $this->year, 1, 1 );
		$end_date   = $start_date->copy()->addYear();

		$response = Factory::create_pricing_calendar( $this->rates->all() )
			->getEventsItemized( $start_date, $end_date, Calendar::BAT_DAILY );

		return Collection::make( $response )
			->map(function( $item ) {
				return $item[ Calendar::BAT_DAY ];
			})->all();
	}

	/**
	 * Build classess for a day.
	 *
	 * @param  Carbonate $working_day Working day.
	 * @return array
	 */
	public function classes_for_a_day( Carbonate $working_day ) {
		$classes = [];
		$today = Carbonate::today();

		// Is current day is today, future or past.
		if ( $working_day->isToday() ) {
			$classes[] = 'abkngcal__day--today';
		} elseif ( $working_day->lt( $today ) ) {
			$classes[] = 'abkngcal__day--past';
		} elseif ( $working_day->gt( $today ) ) {
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
