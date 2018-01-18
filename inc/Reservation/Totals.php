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
	 * Subtotal amount.
	 *
	 * @var \AweBooking\Support\Decimal
	 */
	protected $subtotal;

	/**
	 * Constructor.
	 *
	 * @param \AweBooking\Reservation\Reservation $reservation The reservation instance.
	 */
	public function __construct( Reservation $reservation ) {
		$this->reservation = $reservation;

		$this->calculate();
	}

	/**
	 * Get the subtotal amount.
	 *
	 * @return \AweBooking\Support\Decimal
	 */
	public function get_subtotal() {
		return $this->subtotal;
	}

	/**
	 * Run all calculations methods on the given items in sequence.
	 *
	 * @return void
	 */
	protected function calculate() {
		$this->calculate_item_totals();
	}

	/**
	 * Calculate item totals.
	 *
	 * @return void
	 */
	protected function calculate_item_totals() {
		$zero = Decimal::zero();

		$this->subtotal = $this->reservation->get_rooms()
			->reduce( function( $total, $item ) {
				return $total->add( $item->get_pricing()->get_amount() );
			}, $zero );
	}
}
