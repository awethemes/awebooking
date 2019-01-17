<?php

namespace AweBooking\Admin\Forms;

use AweBooking\Model\Booking\Payment_Item;
use AweBooking\Component\Form\Form;

class Booking_Payment_Form extends Form {
	/**
	 * Constructor.
	 *
	 * @param \AweBooking\Model\Booking\Payment_Item $model The model.
	 */
	public function __construct( Payment_Item $model ) {
		parent::__construct( 'payment-form', $model );
	}

	/**
	 * {@inheritdoc}
	 */
	protected function setup_fields() {
		$this->add_field([
			'id'          => 'amount',
			'type'        => 'abrs_amount',
			'name'        => esc_html__( 'Amount', 'awebooking' ),
		]);

		$this->add_field([
			'id'          => 'method',
			'type'        => 'select',
			'name'        => esc_html__( 'Payment method', 'awebooking' ),
			'default'     => 'cash',
			'options_cb'  => 'abrs_list_payment_methods',
			'show_option_none' => esc_html__( 'N/A', 'awebooking' ),
		]);

		$this->add_field([
			'id'              => 'transaction_id',
			'type'            => 'text',
			'name'            => esc_html__( 'Transaction ID', 'awebooking' ),
			'sanitization_cb' => 'absint',
			// 'deps'        => [ 'method', 'any', $this->get_gateways_support( 'transaction_id' ) ],
		]);

		$this->add_field([
			'id'          => 'is_deposit',
			'type'        => 'abrs_toggle',
			'name'        => esc_html__( 'Deposit', 'awebooking' ),
			'desc'        => esc_html__( 'Is this deposit?', 'awebooking' ),
		]);

		$this->add_field([
			'id'              => 'comment',
			'type'            => 'textarea',
			'name'            => esc_html__( 'Comment', 'awebooking' ),
			'sanitization_cb' => 'abrs_sanitize_html',
			'attributes'      => [
				'rows' => 5,
			],
		]);
	}

	/**
	 * Get gateways support a speical meta for the deps.
	 *
	 * @param  string|array $type The meta type.
	 * @return string
	 */
	protected function get_gateways_support( $type = 'transaction_id' ) {
		$gateways = abrs_payment_gateways()
			->get_enabled()
			->filter( function( $gateway ) use ( $type ) {
				return $gateway->is_support( $type );
			})->keys();

		return implode( ',', $gateways->all() );
	}
}
