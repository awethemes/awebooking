<?php
namespace AweBooking\Gateway;

use Awethemes\Http\Request;
use AweBooking\Model\Booking;

class Check_Payment_Gateway extends Gateway {
	/**
	 * The gateway unique ID.
	 *
	 * @var string
	 */
	protected $method = 'check_payment';

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->method_title = esc_html__( 'Check Payment', 'awebooking' );
		$this->method_description = esc_html__( 'Allows check payments. Why would you take checks in this day and age? Well you probably would not, but it does allow you to make test purchases for testing order emails and the success pages.', 'awebooking' );
	}

	/**
	 * {@inheritdoc}
	 */
	public function setup() {
		$this->setting_fields();

		$this->enabled     = (bool) $this->get_option( 'enabled' );
		$this->title       = $this->get_option( 'title' );
		$this->description = $this->get_option( 'description' );
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
				'default' => true,
			],
			'title' => [
				'name'        => esc_html__( 'Title', 'awebooking' ),
				'type'        => 'text',
				'description' => esc_html__( 'This controls the title which the user sees during checkout.', 'awebooking' ),
				'default'     => _x( 'Check payments', 'Check payment method', 'awebooking' ),
			],
			'description' => [
				'name'        => esc_html__( 'Description', 'awebooking' ),
				'type'        => 'textarea',
				'description' => esc_html__( 'Payment method description that the customer will see on your checkout.', 'awebooking' ),
				'default'     => esc_html__( 'Please send a check to Store Name, Store Street, Store Town, Store State / County, Store Postcode.', 'awebooking' ),
			],
			'instructions' => [
				'name'        => esc_html__( 'Instructions', 'awebooking' ),
				'type'        => 'textarea',
				'description' => esc_html__( 'Instructions that will be added to the thank you page and emails.', 'awebooking' ),
				'default'     => '',
			],
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function process( Booking $booking ) {
		//...
	}
}
