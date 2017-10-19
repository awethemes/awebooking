<?php
namespace AweBooking\Admin\Calendar;

use AweBooking\Booking\Booking;
use AweBooking\Support\Carbonate;
use AweBooking\Support\Abstract_Calendar;

class Booking_Calendar extends Abstract_Calendar {
	/**
	 * The booking instance.
	 *
	 * @var Booking
	 */
	protected $booking;

	/**
	 * Booking periods collection.
	 *
	 * @var Period_Collection
	 */
	protected $periods;

	/**
	 * Create the Calendar.
	 *
	 * @param Booking $booking The exists booking instance.
	 * @param array   $options The calendar options.
	 */
	public function __construct( Booking $booking, array $options = [] ) {
		$this->booking = $booking;
		$this->periods = $this->booking->get_period_collection();

		parent::__construct(
			array_merge( $options, [ 'base_class' => 'aweminical'] )
		);
	}

	/**
	 * Display the Calendar.
	 *
	 * @return void
	 */
	public function display() {
		$date = $this->periods->count() > 0
			? $this->periods->first()->get_start_date()
			: Carbonate::today();

		echo '<div class="' . esc_attr( $this->get_html_class( '&__heading' ) ) . '">', $date->format( 'F Y' ) ,'</div>';

		// @codingStandardsIgnoreLine
		print $this->generate_month_calendar( $date->startOfMonth() );
	}

	/**
	 * Prepare setup the data.
	 *
	 * @param  mixed  $input   Mixed input data.
	 * @param  string $context Context from Calendar.
	 * @return mixed
	 */
	protected function prepare_data( $input, $context ) {
		$flatten = [];

		foreach ( $this->periods as $index => $period ) {
			$i = 0;

			$flatten[ $index ][ $i ] = [
				'START' => $period->get_start_date(),
			];

			foreach ( $period->get_period() as $day ) {
				$dayofweek = calendar_week_mod( $day->dayOfWeek - $this->week_begins );

				$flatten[ $index ][ $i ]['END'] = $day->copy()->addDay();

				if ( 6 === $dayofweek ) {
					$i++;
					continue;
				}
			}
		}

		var_dump($flatten);
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
		$events = [];
		foreach ( $this->periods as $index => $period ) {
			if ( $date->between( $period->get_start_date(), $period->get_end_date() ) ) {
				$events[ $index ] = $this->generate_line_item_events( $date, $period );
			}
		}

		return '<span>%1$s ' . implode( ' ', $events ) . '</span>';
	}

	protected function generate_line_item_events( $date, $period ) {
		static $next_marker;

		// @codingStandardsIgnoreLine
		$dayofweek  = calendar_week_mod( $date->dayOfWeek - $this->week_begins );
		$endofmonth = $date->copy()->endOfMonth();

		$left_columns = 7 - $dayofweek;
		if ( $date->copy()->addDays( $left_columns )->isNextMonth() ) {
			$left_columns = calendar_week_mod( $endofmonth->dayOfWeek - $this->week_begins ) + 1;
		}

		// Create a "marker".
		$marker = false;
		$marker_width = 0;

		if ( $period->get_start_date()->eq( $date ) ) {
			$marker = true;
		} elseif ( 0 == $dayofweek ) {
			$marker = true;
		}

		// If period less than or equals padding column left.
		if ( $marker ) {
			$width = min( $left_columns, $period->nights() );
			return '<i class="sevent event-start event-end" style="width:' . ( $width * 100 ) . '%%"></i>';
		}

		return;
	}
}
