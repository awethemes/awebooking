<?php

namespace AweBooking\Admin\Calendar;

use AweBooking\Factory;
use AweBooking\Pricing\Rate;
use AweBooking\Pricing\Price;
use AweBooking\Hotel\Room_Type;
use AweBooking\Booking\Calendar;
use AweBooking\Support\Carbonate;
use AweBooking\Support\Collection;
use AweBooking\Support\Abstract_Calendar;
use Illuminate\Support\Arr;

class Pricing_Calendar extends Abstract_Calendar {
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
	 * The year we will working on.
	 *
	 * @var int
	 */
	protected $year;

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
	public function __construct( Room_Type $room_type, $year = null ) {
		parent::__construct();

		$this->room_type = $room_type;
		$this->rates = $this->room_type->get_rates();

		$this->year = $year ? absint( $year ) : absint( date( 'Y' ) );
	}

	/**
	 * Display the Calendar.
	 *
	 * @return void
	 */
	public function display() {
		echo '<div class="abkngcal-container abkngcal-container--fullwidth abkngcal--pricing-calendar" data-unit="' . esc_attr( $this->room_type->get_id() ) . '">
				<div class="abkngcal-ajax-loading" style="display: none;"><div class="spinner"></div></div>';

		// @codingStandardsIgnoreStart
		$checkbox = sprintf( '<span class="check-column"><input type="checkbox" name="bulk-update[]" value="%s" /></span>', esc_attr( $this->room_type->get_id() ) );
		echo '<h2>' . $checkbox . esc_html( $this->room_type->get_title() ) . '</h2>';
		echo $this->generate_year_calendar( $this->year );
		// @codingStandardsIgnoreEnd

		echo '</div>';
	}

	/**
	 * Prepare setup the data.
	 *
	 * @param  mixed  $input   Mixed input data.
	 * @param  string $context Context from Calendar.
	 * @return mixed
	 */
	protected function prepare_data( $input, $context ) {
		$start_date = Carbonate::createFromDate( $input, 1, 1 );
		$end_date   = $start_date->copy()->addYear();

		$response = Factory::create_pricing_calendar( $this->rates->all() )
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
		$contents = '<span class="' . esc_attr( $this->get_html_class( '&__night-selection' ) ) . '"></span>';

		$rate = $this->room_type->get_standard_rate();
		$getdata = $rate->get_id() . '.' . $date->format( 'Y.n.\dj' );

		if ( Arr::has( $this->data, $getdata ) ) {
			$price = Price::from_integer( Arr::get( $this->data, $getdata ) );
			$rate_price = $rate->get_base_price();

			if ( ! $price->equals( $rate_price ) ) {
				$contents .= '<span class="abkngcal__price-change">' . $price->get_amount() . '</span>';
			} else {
				$contents .= '<span class="abkngcal__price">' . $price->get_amount() . '</span>';
			}
		}

		return $contents;
	}
}
