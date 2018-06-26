<?php
namespace AweBooking\Reservation;

use AweBooking\Support\Decimal;

class Totals {
	/**
	 * Stores totals.
	 *
	 * @var array
	 */
	protected $totals = [
		'total'           => 0,
		'subtotal'        => 0,
		'rooms_total'     => 0,
		'rooms_subtotal'  => 0,
		'rooms_total_tax' => 0,
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
	 * @return array
	 */
	public function totals() {
		return $this->totals;
	}

	/**
	 * Gets a line total.
	 *
	 * @param  string $key Total to get.
	 * @return \AweBooking\Support\Decimal
	 */
	public function get( $key = 'total' ) {
		$totals = $this->totals();

		return isset( $totals[ $key ] ) ? $totals[ $key ] : 0;
	}

	/**
	 * Sets a single total.
	 *
	 * @param string $key The total name.
	 * @param int    $total Total to set.
	 */
	protected function set( $key = 'total', $total ) {
		$this->totals[ $key ] = $total instanceof Decimal ? $total->as_numeric() : $total;
	}

	/**
	 * Run all calculations methods on the given items in sequence.
	 *
	 * @return void
	 */
	public function calculate() {
		$this->prepare_calculate();
		$this->calculate_rooms();
		$this->calculate_totals();
	}

	/**
	 * Prepare calculate.
	 *
	 * @return void
	 */
	protected function prepare_calculate() {
		/* @var \AweBooking\Reservation\Item $room_stay */
		foreach ( $this->reservation->get_room_stays() as $room_stay ) {
			/* @var \AweBooking\Model\Pricing\Contracts\Rate $rate_plan */
			$rate_plan = $room_stay->data()->get_rate_plan();

			// Get total price.
			$price = abrs_decimal( $room_stay->get( 'price' ) )
				->mul( $room_stay->get( 'quantity' ) )
				->as_numeric();

			$room_stay->set( 'tax', 0 );
			$room_stay->set( 'price_includes_tax', $rate_plan->price_includes_tax() );

			if ( abrs_tax_enabled() && $rate_plan->is_taxable() ) {
				if ( 'single' === abrs_get_tax_rate_model() ) {
					$tax_rate_id = abrs_get_option( 'single_tax_rate' );
				} else {
					$tax_rate_id = $rate_plan->get_tax_rate();
				}

				$room_stay->set( 'tax_rates', $this->filter_tax_rates(
					apply_filters( 'abrs_room_stay_tax_rates', [ $tax_rate_id ], $room_stay )
				));
			}
		}
	}

	/**
	 * Calculate room totals & subtotals.
	 *
	 * Subtotals are costs before discounts.
	 *
	 * @return void
	 */
	protected function calculate_rooms() {
		$room_stays = $this->reservation->get_room_stays();

		/* @var \AweBooking\Reservation\Item $room_stay */
		foreach ( $room_stays as $room_stay ) {
			if ( abrs_tax_enabled() && count( $room_stay['tax_rates'] ) > 0 ) {
				$total_taxes = abrs_calc_tax( $room_stay->get( 'price' ), $room_stay->get( 'tax_rates' ), $room_stay->is_price_includes_tax() );
				$room_stay->set( 'tax', array_sum( array_map( 'abrs_round_tax', $total_taxes ) ) );
			}
		}

		$this->set( 'rooms_total', $room_stays->sum( 'total' ) );
		$this->set( 'rooms_subtotal', $room_stays->sum( 'subtotal' ) );
		$this->set( 'rooms_total_tax', $room_stays->sum( 'total_tax' ) );
	}

	/**
	 * Main cart totals.
	 *
	 * @return void
	 */
	protected function calculate_totals() {
		$this->set( 'subtotal', $this->get( 'rooms_subtotal' ) );
		$this->set( 'total', $this->get( 'rooms_total' ) );
	}

	/**
	 * Filter tax rates.
	 *
	 * @param  array $rates The rates.
	 * @return array
	 */
	protected function filter_tax_rates( $rates ) {
		return abrs_collect( $rates )
			->map( function ( $rate ) {
				return is_numeric( $rate ) ? abrs_get_tax_rate( $rate ) : $rate;
			})
			->reject( function ( $rate ) {
				return ! isset( $rate['id'], $rate['rate'], $rate['compound'] );
			})
			->sortBy( 'priority' )
			->keyBy( 'id' )
			->all();
	}
}
