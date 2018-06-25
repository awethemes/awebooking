<?php
namespace AweBooking\Reservation;

use AweBooking\Availability\Room_Rate;
use AweBooking\Support\Decimal;

class Totals {
	/**
	 * Stores totals.
	 *
	 * @var array
	 */
	protected $totals = [
		'subtotal'       => 0,
		'subtotal_tax'   => 0,
		'discount_total' => 0,
		'discount_tax'   => 0,
		'rooms_total'    => 0,
		'rooms_tax'      => 0,
		'total'          => 0,
		'total_tax'      => 0,
	];

	/**
	 * The reservation instance.
	 *
	 * @var \AweBooking\Reservation\Reservation
	 */
	protected $reservation;

	/**
	 * Constructor.
	 *
	 * @param \AweBooking\Reservation\Reservation $reservation The reservation instance.
	 */
	public function __construct( Reservation $reservation ) {
		$this->reservation = $reservation;
	}

	/**
	 * Get all totals.
	 *
	 * @return array \AweBooking\Support\Decimal[]
	 */
	public function totals() {
		return array_map( function ( $total ) {
			return ! $total instanceof Decimal ? abrs_decimal( $total ) : $total;
		}, $this->totals );
	}

	/**
	 * Get a single total.
	 *
	 * @param  string $key Total to get.
	 * @return \AweBooking\Support\Decimal
	 */
	public function get( $key = 'total' ) {
		$totals = $this->totals();

		return isset( $totals[ $key ] ) ? $totals[ $key ] : abrs_decimal( 0 );
	}

	/**
	 * Set a single total.
	 *
	 * @param string $key The total name.
	 * @param int    $total Total to set.
	 */
	protected function set( $key = 'total', $total ) {
		$this->totals[ $key ] = ! $total instanceof Decimal ? abrs_decimal( $total ) : $total;
	}

	/**
	 * Run all calculations methods on the given items in sequence.
	 *
	 * @return void
	 */
	public function calculate() {
		$this->calculate_tax_rates();
		$this->calculate_room_totals();
	}

	/**
	 * Calculate tax rates.
	 *
	 * @return void
	 */
	protected function calculate_tax_rates() {
		/* @var \AweBooking\Reservation\Item $room_stay */
		foreach ( $this->reservation->get_room_stays() as $room_stay ) {
			/* @var \AweBooking\Availability\Room_Rate $room_rate */
			$room_rate = $room_stay->get_data();

			$room_stay->set( 'taxable', abrs_tax_enabled() );
			$room_stay->set( 'price_includes_tax', abrs_prices_includes_tax() );

			if ( $room_stay['taxable'] && $tax_rate = $room_rate->get_tax_rate() ) {
				$room_stay->set( 'tax_rate', $tax_rate );
			}
		}
	}

	/**
	 * Calculate room totals.
	 *
	 * Subtotals are costs before discounts.
	 *
	 * @return void
	 */
	protected function calculate_room_totals() {
		$total = $subtotal = abrs_decimal( 0 );

		foreach ( $this->reservation->get_room_stays() as $room_stay ) {
			$subtotal = $subtotal->add( $room_stay->get_total_price_exc_tax() );
			$total = $total->add( $room_stay->get_total_price() );
		}

		$this->set( 'rooms_subtotal', $subtotal );
		$this->set( 'total', $total );
	}
}
