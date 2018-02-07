<?php
namespace AweBooking\Admin\Forms;

use AweBooking\Dropdown;

class Create_Payment_Form extends Form_Abstract {
	/**
	 * The form ID.
	 *
	 * @var string
	 */
	protected $form_id = 'awebooking_new_reservation_source';

	/**
	 * {@inheritdoc}
	 */
	protected function fields() {
		$this->add_field([
			'id'          => 'amount',
			'type'        => 'text_small',
			'name'        => esc_html__( 'Amount', 'awebooking' ),
			'append'      => awebooking( 'currency' )->get_symbol(),
			'validate'    => 'required|numeric|min:0',
		]);

		$this->add_field([
			'id'          => 'method',
			'type'        => 'select',
			'name'        => esc_html__( 'Payment method', 'awebooking' ),
			'options_cb'  => Dropdown::cb( 'get_payment_methods' ),
			'default'     => 'cash',
		]);

		$this->add_field([
			'id'          => 'transaction_id',
			'type'        => 'text',
			'name'        => esc_html__( 'Transaction ID', 'awebooking' ),
			'deps'        => [ 'method', 'any', $this->get_gateways_support( 'transaction_id' ) ],
		]);

		$this->add_field([
			'id'          => 'comment',
			'type'        => 'textarea',
			'name'        => esc_html__( 'Comment', 'awebooking' ),
			'attributes'  => [
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
		$gateways = awebooking()->make( 'gateways' )->enabled()
			->filter( function( $gateway ) use ( $type ) {
				return $gateway->is_support( $type );
			})->keys();

		return implode( ',', $gateways->all() );
	}
}
