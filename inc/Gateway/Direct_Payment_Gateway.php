<?php
namespace AweBooking\Gateway;

use Awethemes\Http\Request;
use AweBooking\Model\Booking;

class Direct_Payment_Gateway extends Gateway {
	/**
	 * The gateway unique ID.
	 *
	 * @var string
	 */
	protected $method = 'direct_payment';

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
		$this->method_title = esc_html__( 'Direct Payment', 'awebooking' );
		$this->method_description = esc_html__( 'Payment directly at the hotel.', 'awebooking' );
	}

	/**
	 * {@inheritdoc}
	 */
	public function setup() {
		$this->setting_fields();

		$this->enabled     = $this->get_option( 'enabled' );
		$this->title       = $this->get_option( 'title' );
		$this->description = $this->get_option( 'description' );
	}

	/**
	 * {@inheritdoc}
	 */
	public function process( Booking $booking, Request $request ) {
		$booking->update_status( 'on-hold' );

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
				'name'    => esc_html__( 'Enable / Disable', 'awebooking' ),
				'type'    => 'toggle',
				'label'   => esc_html__( 'Enable check payments', 'awebooking' ),
				'default' => 'off',
			],
			'title' => [
				'name'        => esc_html__( 'Title', 'awebooking' ),
				'type'        => 'text',
				'description' => esc_html__( 'This controls the title which the user sees during checkout.', 'awebooking' ),
				'default'     => esc_html__( 'Pay at Hotel', 'awebooking' ),
			],
			'description' => [
				'name'        => esc_html__( 'Description', 'awebooking' ),
				'type'        => 'textarea',
				'description' => esc_html__( 'Payment method description that the customer will see on your checkout.', 'awebooking' ),
				'attributes'  => [ 'style' => 'height: 80px;' ],
			],
			'instructions' => [
				'name'        => esc_html__( 'Instructions', 'awebooking' ),
				'type'        => 'textarea',
				'description' => esc_html__( 'Instructions that will be added to the thank you page and emails.', 'awebooking' ),
				'attributes'  => [ 'style' => 'height: 80px;' ],
			],
		];
	}
}
