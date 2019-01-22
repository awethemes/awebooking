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
	 * Should taxes be calculated?
	 *
	 * @var boolean
	 */
	protected $calculate_tax = true;

	/**
	 * Stores totals.
	 *
	 * @var array
	 */
	protected $totals = [
		'total'              => 0,
		'subtotal'           => 0,
		'rooms_total'        => 0,
		'rooms_subtotal'     => 0,
		'rooms_total_tax'    => 0,
		'services_total'     => 0,
		'services_subtotal'  => 0,
		'services_total_tax' => 0,
		'fees_total'         => 0,
		'fees_tax'           => 0,
	];

	/**
	 * Constructor.
	 *
	 * @param \AweBooking\Reservation\Reservation $reservation The reservation instance.
	 */
	public function __construct( Reservation $reservation ) {
		$this->reservation   = $reservation;
		$this->calculate_tax = abrs_tax_enabled();
	}

	/**
	 * //
	 *
	 * @return \AweBooking\Reservation\Reservation
	 */
	public function get_reservation() {
		return $this->reservation;
	}

	/**
	 * Gets a line total.
	 *
	 * @param  string $key Total to get.
	 * @return float
	 */
	public function get( $key = 'total' ) {
		$totals = $this->totals();

		return isset( $totals[ $key ] ) ? $totals[ $key ] : 0;
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
	 * Run all calculations methods on the given items in sequence.
	 *
	 * @return void
	 */
	public function calculate() {
		$this->prepare_calculate();
		$this->calculate_rooms();
		$this->calculate_services();
		$this->calculate_fees();
		$this->calculate_totals();
	}

	/**
	 * Prepare calculate.
	 *
	 * @return void
	 */
	protected function prepare_calculate() {
		// ...
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
			/* @var \AweBooking\Model\Pricing\Contracts\Rate $rate_plan */
			$rate_plan = $room_stay->data()->get_rate_plan();

			$room_stay->set( 'tax', 0 );
			$room_stay->set( 'price_includes_tax', $rate_plan->price_includes_tax() );

			if ( abrs_tax_enabled() && $rate_plan->is_taxable() ) {
				if ( 'single' === abrs_get_tax_rate_model() ) {
					$tax_rate_id = abrs_get_option( 'single_tax_rate' );
				} else {
					$tax_rate_id = $rate_plan->get_tax_rate();
				}

				$taxes = $this->filter_tax_rates(
					apply_filters( 'abrs_room_stay_tax_rates', [ $tax_rate_id ], $room_stay )
				);

				$total_taxes = abrs_calc_tax( $room_stay->get( 'price' ), $taxes, $room_stay->is_price_includes_tax() );

				$room_stay->set( 'tax_rates', $total_taxes );
				$room_stay->set( 'tax', array_sum( array_map( 'abrs_round_tax', $total_taxes ) ) );
			}
		}

		$this->set_total( 'rooms_total', $this->sum_items( $room_stays, 'total' ) );
		$this->set_total( 'rooms_subtotal', $this->sum_items( $room_stays, 'subtotal' ) );
		$this->set_total( 'rooms_total_tax', $this->sum_items( $room_stays, 'total_tax' ) );
	}

	/**
	 * Calculate services costs.
	 *
	 * @return void
	 */
	protected function calculate_services() {
		$services = $this->reservation->get_services();

		$res_request = $this->reservation->get_previous_request();

		/* @var \AweBooking\Reservation\Item $item */
		foreach ( $services as $item ) {
			if ( $item['included'] ) {
				$item->set( 'price', 0 );
				continue;
			}

			/* @var \AweBooking\Model\Service $service */
			$service = $item->model();

			$price = abrs_calc_service_price( $service, [
				'nights'     => $res_request->nights,
				'base_price' => $this->get( 'rooms_subtotal' ),
			]);

			$item->set( 'price', $price );
		}
		$this->set_total( 'services_total', $this->sum_items( $services, 'total' ) );
		$this->set_total( 'services_total_tax', $this->sum_items( $services, 'total_tax' ) );
	}

	/**
	 * Calculate fees costs.
	 *
	 * @return void
	 */
	protected function calculate_fees() {
		$this->reservation->calculate_fees();
		$fees = $this->reservation->get_fees();

		$fee_running_total = 0;

		foreach ( $fees as $key => $fee ) {
			// Correct some property.
			$fee->quantity           = 1;
			$fee->price_includes_tax = false;

			// Negative fees should not make the total go negative.
			if ( $fee->price < 0 ) {
				$max_discount = ( $this->get( 'rooms_total' ) + $this->get( 'services_total' ) + $fee_running_total ) * - 1;

				if ( $fee->price < $max_discount ) {
					$fee->price = $max_discount;
				}
			}

			$fee_running_total += $fee->price;
		}

		$this->set_total( 'fees_total', $this->sum_items( $fees, 'price' ) );
		$this->set_total( 'fees_total_tax', $this->sum_items( $fees, 'total_tax' ) );
	}

	/**
	 * Main cart totals.
	 *
	 * @return void
	 */
	protected function calculate_totals() {
		$this->set_total( 'subtotal', $this->get( 'rooms_subtotal' ) + $this->get( 'services_subtotal' ) + $this->get( 'fees_total' ) );
		$this->set_total( 'total', $this->get( 'rooms_total' ) + $this->get( 'services_total' ) + $this->get( 'fees_total' ) );
	}

	/**
	 * Sets a single total.
	 *
	 * @param string $key   The total name.
	 * @param int    $total Total to set.
	 *
	 * @return $this
	 */
	public function set_total( $key, $total ) {
		$this->totals[ $key ] = $total instanceof Decimal ? $total->as_numeric() : $total;

		return $this;
	}

	/**
	 * Sum the items costs.
	 *
	 * @param \AweBooking\Support\Collection $items The items.
	 * @param string                         $key   The string of key.
	 *
	 * @return int|float
	 */
	protected function sum_items( $items, $key = 'total' ) {
		if ( $items->isEmpty() ) {
			return 0;
		}

		/* @var $total \AweBooking\Support\Decimal */
		$total = $items->reduce( function ( Decimal $total, $item ) use ( $key ) {
			return $total->add( $item->{$key} );
		}, abrs_decimal( 0 ) );

		return $total->as_numeric();
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
