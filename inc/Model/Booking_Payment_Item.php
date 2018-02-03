<?php
namespace AweBooking\Model;

use AweBooking\Dropdown;
use AweBooking\Support\Decimal;
use AweBooking\Support\Utils as U;
use AweBooking\Support\Carbonate;

class Booking_Payment_Item extends Booking_Item {
	/**
	 * Name of object type.
	 *
	 * @var string
	 */
	protected $object_type = 'booking_payment_item';

	/**
	 * The attributes for this object.
	 *
	 * @var array
	 */
	protected $extra_attributes = [
		'method'    => '',
		'amount'    => 0,
		'comment'   => '',
		'date_paid' => null,

		// For online payments.
		'transaction_id' => '',
	];

	/**
	 * The attributes that should be cast to native types.
	 *
	 * @var array
	 */
	protected $extra_casts = [
		'amount' => 'float',
	];

	/**
	 * An array of attributes mapped with metadata.
	 *
	 * @var array
	 */
	protected $maps = [
		'method'         => '_payment_method',
		'amount'         => '_payment_amount',
		'comment'        => '_payment_comment',
		'date_paid'      => '_payment_date_paid',
		'transaction_id' => '_payment_transaction_id',
	];

	/**
	 * Returns booking item type.
	 *
	 * @return string
	 */
	public function get_type() {
		return 'payment_item';
	}

	public function get_amount() {
		return apply_filters( $this->prefix( 'get_amount' ), Decimal::create( $this['amount'] ), $this );
	}

	/**
	 * Get the payment method title.
	 *
	 * @return string
	 */
	public function get_payment_method_title() {
		$method = $this['method'];

		if ( empty( $method ) ) {
			$payment_method = esc_html__( 'N/A', 'awebooking' );
		} else {
			$dropdown       = Dropdown::get_payment_methods();
			$payment_method = array_key_exists( $method, $dropdown ) ? $dropdown[ $method ] : $method;
		}

		return apply_filters( $this->prefix( 'get_payment_method_title' ), $payment_method, $this );
	}

	/**
	 * Get the date_paid as Carbonate instance.
	 *
	 * @return \AweBooking\Support\Carbonate
	 */
	public function get_date_paid() {
		$date_paid = U::rescue(
			function() {
				return Carbonate::create( $this['date_paid'] );
			},
			function() {
				return Carbonate::now();
			}
		);

		return apply_filters( $this->prefix( 'get_amount' ), $date_paid, $this );
	}

	public function get_delete_link( $nonce = true ) {
		$delete_link = awebooking( 'url' )->admin_route(
			$this->format_permalink( '/booking/%1$d/payment/%2$d' )
		);

		if ( $nonce ) {
			$delete_link = wp_nonce_url( $delete_link, 'delete_payment_item' );
		}

		return apply_filters( $this->prefix( 'get_delete_link' ), $delete_link, $this );
	}

	public function get_edit_link() {
		$delete_link = awebooking( 'url' )->admin_route(
			$this->format_permalink( '/booking/%1$d/payment/%2$d/edit' )
		);

		return apply_filters( $this->prefix( 'get_edit_link' ), $delete_link, $this );
	}

	/**
	 * [format_permalink description]
	 *
	 * %1$d - The Booking ID.
	 * %2$d - The payment item ID.
	 *
	 * @param  [type] $format [description]
	 * @return [type]
	 */
	protected function format_permalink( $format ) {
		return sprintf( $format, $this->get_booking()->get_id(), $this->get_id() );
	}
}