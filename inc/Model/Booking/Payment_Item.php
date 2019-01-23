<?php

namespace AweBooking\Model\Booking;

class Payment_Item extends Item {
	/**
	 * Name of object type.
	 *
	 * @var string
	 */
	protected $object_type = 'payment_item';

	/**
	 * Name of item type.
	 *
	 * @var string
	 */
	protected $type = 'payment_item';

	/**
	 * Is this payment is deposit or not?
	 *
	 * @return boolean
	 */
	public function is_deposit() {
		return 'on' === $this['is_deposit'];
	}

	/**
	 * Get the payment method title.
	 *
	 * @return string
	 */
	public function get_method_title() {
		$method = $this->get( 'method' );

		if ( empty( $method ) ) {
			$payment_method = esc_html__( 'N/A', 'awebooking' );
		} else {
			$dropdown       = abrs_list_payment_methods();
			$payment_method = array_key_exists( $method, $dropdown ) ? $dropdown[ $method ] : $method;
		}

		return apply_filters( $this->prefix( 'get_method_title' ), $payment_method, $this );
	}

	/**
	 * Perform update attributes when inserting.
	 *
	 * @return void
	 */
	protected function inserting() {
		if ( empty( $this->attributes['date_paid'] ) ) {
			$this->attributes['date_paid'] = current_time( 'mysql' );
		} else {
			$this->attributes['date_paid'] = (string) abrs_date_time( $this->attributes['date_paid'] );
		}
	}

	/**
	 * Perform update attributes when update.
	 *
	 * @return void
	 */
	protected function updating() {
		$this->attributes['name'] = $this->get_method_title();
	}

	/**
	 * {@inheritdoc}
	 */
	protected function setup_attributes() {
		$this->attributes = apply_filters( $this->prefix( 'attributes' ), array_merge( $this->attributes, [
			'method'          => '',
			'amount'          => 0,
			'comment'         => '',
			'is_deposit'      => 'off',
			'date_paid'       => null,
			'transaction_id'  => '',
			'transaction_log' => [],
		]));
	}

	/**
	 * {@inheritdoc}
	 */
	protected function map_attributes() {
		$this->maps = apply_filters( $this->prefix( 'map_attributes' ), [
			'method'          => '_payment_method',
			'amount'          => '_payment_amount',
			'comment'         => '_payment_comment',
			'is_deposit'      => '_payment_is_deposit',
			'date_paid'       => '_payment_date_paid',
			'transaction_id'  => '_payment_transaction_id',
			'transaction_log' => '_payment_transaction_log',
		]);
	}

	/**
	 * {@inheritdoc}
	 */
	protected function sanitize_attribute( $key, $value ) {
		switch ( $key ) {
			case 'amount':
				$value = abrs_sanitize_decimal( $value );
				break;

			case 'date_paid':
				$value = (string) abrs_date_time( $value );
				break;

			case 'is_deposit':
				$value = abrs_sanitize_checkbox( $value );
				break;

			case 'comment':
				$value = abrs_sanitize_html( $value );
				break;
		}

		return parent::sanitize_attribute( $key, $value );
	}
}
