<?php
namespace AweBooking\Gateway;

use Awethemes\Http\Request;
use AweBooking\Model\Booking;

class BACS_Gateway extends Gateway {
	/**
	 * The gateway unique ID.
	 *
	 * @var string
	 */
	protected $method = 'bacs';

	/**
	 * The extra metadata this gateway support.
	 *
	 * @var array
	 */
	public $supports = [ 'transaction_id' ];

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->method_title = esc_html__( 'BACS', 'awebooking' );
		$this->method_description = esc_html__( 'Allows payments by BACS, more commonly known as direct bank/wire transfer.', 'awebooking' );
	}

	/**
	 * {@inheritdoc}
	 */
	public function setup() {
		$this->setting_fields();

		$this->enabled     = $this->get_option( 'enabled' );
		$this->title       = esc_html( $this->get_option( 'title' ) );
		$this->description = esc_textarea( $this->get_option( 'description' ) );
	}

	/**
	 * {@inheritdoc}
	 */
	public function process( Booking $booking, Request $request ) {
		$booking->update_status( 'on-hold', esc_html__( 'Awaiting BACS payment', 'awebooking' ) );

		// Flush the reservation data.
		abrs_reservation()->flush();

		return ( new Response( 'success' ) )->data( $booking );
	}

	/**
	 * Set the gateway settings fields.
	 *
	 * @return void
	 */
	public function setting_fields() {
		$this->setting_fields = [
			'enabled' => [
				'name'        => esc_html__( 'Enable / Disable', 'awebooking' ),
				'type'        => 'toggle',
				'label'       => esc_html__( 'Enable check payments', 'awebooking' ),
				'default'     => 'on',
			],
			'title' => [
				'name'        => esc_html__( 'Title', 'awebooking' ),
				'type'        => 'text',
				'description' => esc_html__( 'This controls the title which the user sees during checkout.', 'awebooking' ),
				'default'     => _x( 'Direct bank transfer', 'BACS payment method', 'awebooking' ),
			],
			'description' => [
				'name'        => esc_html__( 'Description', 'awebooking' ),
				'type'        => 'textarea',
				'description' => esc_html__( 'Payment method description that the customer will see on your checkout.', 'awebooking' ),
				'default'     => esc_html__( 'Make your payment directly into our bank account. Please use your Booking ID as the payment reference.', 'awebooking' ),
			],
			'instructions' => [
				'name'        => esc_html__( 'Instructions', 'awebooking' ),
				'type'        => 'textarea',
				'description' => esc_html__( 'Instructions that will be added to the thank you page and emails.', 'awebooking' ),
				'default'     => '',
			],
			'accounts' => [
				'name'        => esc_html__( 'Account Details', 'awebooking' ),
				'description' => esc_html__( 'Add account details that will be added to the thank you page.', 'awebooking' ),
				'type'        => 'textarea',
				'default'     => '',
			],
		];
	}
}
