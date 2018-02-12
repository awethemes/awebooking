<?php
namespace AweBooking\Model;

use AweBooking\Dropdown;
use AweBooking\Gateway\Manager;
use AweBooking\Support\Decimal;
use AweBooking\Support\Carbonate;
use AweBooking\Support\Utils as U;
use AweBooking\Booking\Items\Booking_Item;

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
		'method'         => '',
		'amount'         => 0,
		'comment'        => '',
		'is_deposit'     => false,
		'date_paid'      => null,
		'transaction_id' => '',
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
		'is_deposit'     => '_payment_is_deposit',
		'date_paid'      => '_payment_date_paid',
		'transaction_id' => '_payment_transaction_id',
	];

	/**
	 * The attributes that should be cast to native types.
	 *
	 * @var array
	 */
	protected $casts = [
		'parent_id'  => 'int',
		'booking_id' => 'int',
		'booking_id' => 'int',
		'is_deposit' => 'boolean',
	];

	/**
	 * The permalinks actions.
	 *
	 * @var array
	 */
	protected $permalinks = [
		'edit'   => '/booking/{booking}/payment/{item}/edit',
		'update' => '/booking/{booking}/payment/{item}',
		'delete' => '/booking/{booking}/payment/{item}',
	];

	/**
	 * Returns booking item type.
	 *
	 * @return string
	 */
	public function get_type() {
		return 'payment_item';
	}

	/**
	 * Get the payment method.
	 *
	 * @return string
	 */
	public function get_method() {
		return apply_filters( $this->prefix( 'get_method' ), $this['method'], $this );
	}

	/**
	 * Get the amount as Decimal.
	 *
	 * @return \AweBooking\Support\Decimal
	 */
	public function get_amount() {
		return apply_filters( $this->prefix( 'get_amount' ), Decimal::create( $this['amount'] ), $this );
	}

	/**
	 * Get the comment.
	 *
	 * @return string
	 */
	public function get_comment() {
		return apply_filters( $this->prefix( 'get_comment' ), $this['comment'], $this );
	}

	/**
	 * Is this payment is deposit or not?
	 *
	 * @return boolean
	 */
	public function is_deposit() {
		return apply_filters( $this->prefix( 'is_deposit' ), $this['is_deposit'], $this );
	}

	/**
	 * Get the transaction ID.
	 *
	 * @return string
	 */
	public function get_transaction_id() {
		return apply_filters( $this->prefix( 'get_comment' ), $this['transaction_id'], $this );
	}

	/**
	 * Get the date_paid as Carbonate instance.
	 *
	 * @return \AweBooking\Support\Carbonate
	 */
	public function get_date_paid() {
		$date_paid = U::rescue( function() {
			return Carbonate::create( $this['date_paid'] );
		}, function() {
			return Carbonate::now();
		});

		return apply_filters( $this->prefix( 'get_amount' ), $date_paid, $this );
	}

	/**
	 * Get the payment method title.
	 *
	 * @return string
	 */
	public function get_method_title() {
		$method = $this->get_method();

		if ( empty( $method ) ) {
			$payment_method = esc_html__( 'N/A', 'awebooking' );
		} else {
			$dropdown       = Dropdown::get_payment_methods();
			$payment_method = array_key_exists( $method, $dropdown ) ? $dropdown[ $method ] : $method;
		}

		return apply_filters( $this->prefix( 'get_method_title' ), $payment_method, $this );
	}

	/**
	 * Resolve the gateway from current payment method.
	 *
	 * @return \AweBooking\Gateway\Gateway
	 */
	public function resolve_gateway() {
		$gateways = awebooking()->make( Manager::class );

		return $gateways->all()->get( $this->get_method() );
	}
}
