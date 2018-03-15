<?php
namespace AweBooking\Model\Concerns;

use AweBooking\Support\Decimal;
use AweBooking\Support\Utils as U;

trait Booking_Payments {
	/**
	 * Get payments of this booking.
	 *
	 * @return array \AweBooking\Support\Collection
	 */
	public function get_payments() {
		return apply_filters( $this->prefix( 'get_payments' ), $this->get_items( 'payment_item' ), $this );
	}

	/**
	 * Get the amount of total paid.
	 *
	 * @return \AweBooking\Support\Decimal
	 */
	public function get_paid() {
		$total_paid = $this->get_payments()->reduce( function( $total, $item ) {
			return $total->add( $item->get_amount() );
		}, Decimal::zero() );

		return apply_filters( $this->prefix( 'get_paid' ), $total_paid, $this );
	}

	/**
	 * Get the amount of balance_due.
	 *
	 * @return \AweBooking\Support\Decimal
	 */
	public function get_balance_due() {
		$balance_due = Decimal::create( $this->get_total() )->sub( $this->get_paid() );

		return apply_filters( $this->prefix( 'get_balance_due' ), $balance_due, $this );
	}
}
