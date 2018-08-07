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
	 * @return float
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
	protected function set( $key, $total ) {
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
		/* @var \AweBooking\Reservation\Item $room_stay */
		foreach ( $this->reservation->get_room_stays() as $room_stay ) {
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
	 * Calculate services costs.
	 *
	 * @return void
	 */
	protected function calculate_services() {
		$services = $this->reservation->get_services();

		$res_request = $this->reservation->get_previous_request();

		/* @var \AweBooking\Reservation\Item $item */
		foreach ( $services as $item ) {
			/* @var \AweBooking\Model\Service $service */
			$service = $item->model();

			if ( $item['included'] ) {
				$item->set( 'price', 0 );
			} else {
				$price = abrs_calc_service_price( $service, [
					'nights'     => $res_request->nights,
					'base_price' => $this->get( 'rooms_subtotal' ),
				]);

				$item->set( 'price', $price );
			}
		}

		$this->set( 'services_total', $services->sum( 'total' ) );
		$this->set( 'services_subtotal', $services->sum( 'subtotal' ) );
		$this->set( 'services_total_tax', $services->sum( 'total_tax' ) );
	}

	/**
	 * Calculate fees costs.
	 *
	 * @return void
	 */
	protected function calculate_fees() {
		// TODO: ...
	}

	/**
	 * Main cart totals.
	 *
	 * @return void
	 */
	protected function calculate_totals() {
		$this->set( 'subtotal', $this->get( 'rooms_subtotal' ) + $this->get( 'services_subtotal' ) + $this->get( 'fees_total' ) );
		$this->set( 'total', $this->get( 'rooms_total' ) + $this->get( 'services_total' ) + $this->get( 'fees_total' ) );
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
