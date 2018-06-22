<?php
namespace AweBooking\Reservation;

use AweBooking\Support\Decimal;

class Totals {
	/**
	 * The reservation instance.
	 *
	 * @var \AweBooking\Reservation\Reservation
	 */
	protected $reservation;

	/**
	 * Stores totals.
	 *
	 * @var array
	 */
	protected $totals = [
		'fees_total'         => 0,
		'fees_total_tax'     => 0,
		'items_subtotal'     => 0,
		'items_subtotal_tax' => 0,
		'items_total'        => 0,
		'items_total_tax'    => 0,
		'total'              => 0,
		'discounts_total'    => 0,
	];

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
		$this->calculate_room_totals();
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

		$this->set( 'items_subtotal', $subtotal );
		$this->set( 'total', $subtotal );
	}
}
