<?php
namespace AweBooking\Admin\Calendar;

use AweBooking\Booking\Booking;
use AweBooking\Support\Period;
use AweBooking\Support\Carbonate;
use AweBooking\Support\Collection;
use AweBooking\Support\Abstract_Calendar;

class Booking_Calendar extends Abstract_Calendar {
	/**
	 * The booking instance.
	 *
	 * @var Booking
	 */
	protected $booking;

	/**
	 * Create the Calendar.
	 *
	 * @param Booking $booking The exists booking instance.
	 * @param array   $options The calendar options.
	 */
	public function __construct( Booking $booking, array $options = [] ) {
		$this->booking = $booking;

		parent::__construct( array_merge( $options, [
			'base_class' => 'aweminical',
		]));
	}

	/**
	 * Display the Calendar.
	 *
	 * @return void
	 */
	public function display() {
		$booking_items = $this->booking->get_line_items();

		$date = $booking_items->count()
			? $booking_items->first()->get_period()->get_start_date()
			: Carbonate::today();

		echo '<div class="' . esc_attr( $this->get_html_class( '&__heading' ) ) . '">', esc_html( $date->format( 'F Y' ) ) ,'</div>';

		// @codingStandardsIgnoreLine
		print $this->generate_month_calendar( $date->startOfMonth() );
	}

	/**
	 * Prepare setup the data.
	 *
	 * @param  mixed  $month   The month.
	 * @param  string $context Context from Calendar.
	 * @return mixed
	 */
	protected function prepare_data( $month, $context ) {
		$booking_items = $this->booking->get_line_items();

		$data = [];
		foreach ( $booking_items as $item ) {
			$period = $item->get_period();

			$segments = Collection::make( $period->segments( $this->week_begins ) )
				->mapWithKeys(function( $segment ) {
					return [ $segment->get_start_date()->toDateString() => $segment ];
				})->reject(function ( $segment ) use ( $month ) {
					return ! $month->isSameMonth( $segment->get_start_date() ) &&
						! $month->isSameMonth( $segment->get_end_date() );
				});

			$data[ $item->get_id() ] = [
				'item'     => $item,
				'period'   => $period,
				'segments' => $segments,
			];
		}

		return $data;
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
		if ( empty( $this->data ) ) {
			return '<span>%1$s</span>';
		}

		$events = [];
		foreach ( $this->data as $item_id => $data ) {
			if ( $data['segments']->has( $date->toDateString() ) ) {
				$period = $data['period'];
				$current_segment = $data['segments']->get( $date->toDateString() );

				if ( ! $current_segment->get_start_date()->isSameMonth( $date ) ) {
					$current_segment = $current_segment->startingOn( $date->copy()->startOfMonth() );
				}

				if ( ! $current_segment->get_end_date()->isSameMonth( $date ) ) {
					$current_segment = $current_segment->endingOn( $date->copy()->endOfMonth() );
				}

				$classes = [];
				$width = $current_segment->nights();

				if ( 1 == count( $data['segments'] ) ) {
					$classes[] = 'event-blala';
				}

				if ( $current_segment->get_start_date()->isSameDay( $period->get_start_date() ) ) {
					$classes[] = 'event-start';
				}

				if ( $current_segment->get_end_date()->isSameDay( $period->get_end_date() ) ) {
					$width++;
					$classes[] = 'event-end';
				} else {
					$classes[] = 'event-continues';
				}

				$events[] = '<i class="sevent ' . esc_attr( implode( ' ', $classes ) ) . '" data-line-item="' . esc_attr( $item_id ) . '" style="width:' . esc_attr( $width * 100 ) . '%%"></i>';
			}
		}

		return '<span>%1$s ' . implode( ' ', $events ) . '</span>';
	}
}
