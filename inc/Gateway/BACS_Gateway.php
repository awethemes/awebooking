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
	protected $supports = [ 'transaction_id' ];

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

		$this->enabled     = (bool) $this->get_option( 'enabled', true );
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
				'default'     => _x( 'Direct bank transfer', 'BACS payment method', 'awebooking' ),
			],
			'description' => [
				'name'        => esc_html__( 'Description', 'awebooking' ),
				'type'        => 'textarea',
				'description' => esc_html__( 'Payment method description that the customer will see on your checkout.', 'awebooking' ),
				'default'     => esc_html__( 'Make your payment directly into our bank account. Please use your Booking ID as the payment reference. Your order will not be shipped until the funds have cleared in our account.', 'awebooking' ),
			],
			'instructions' => [
				'name'        => esc_html__( 'Instructions', 'awebooking' ),
				'type'        => 'textarea',
				'description' => esc_html__( 'Instructions that will be added to the thank you page and emails.', 'awebooking' ),
				'default'     => '',
			],
			'accounts' => [
				'name'        => esc_html__( 'Account detail', 'awebooking' ),
				'type'        => 'group',
				'options'     => [
					'group_title'   => esc_html__( 'Account {#}', 'awebooking' ),
					'add_button'    => esc_html__( 'Add', 'awebooking' ),
					'remove_button' => esc_html__( 'Remove', 'awebooking' ),
					'sortable'      => true,
				],
				'fields'      => [
					[
						'id'          => 'account_name',
						'name'        => esc_html__( 'Account name', 'awebooking' ),
						'type'        => 'input',
					],
					[
						'id'          => 'account_number',
						'name'        => esc_html__( 'Account number', 'awebooking' ),
						'type'        => 'input',
					],
					[
						'id'          => 'bank_name',
						'name'        => esc_html__( 'Bank name', 'awebooking' ),
						'type'        => 'input',
					],
					[
						'id'          => 'sort_code',
						'name'        => esc_html__( 'Sort code', 'awebooking' ),
						'type'        => 'input',
					],
					[
						'id'          => 'iban',
						'name'        => esc_html__( 'IBAN', 'awebooking' ),
						'type'        => 'input',
					],
					[
						'id'          => 'bic_swift',
						'name'        => esc_html__( 'BIC / Swift', 'awebooking' ),
						'type'        => 'input',
					],
				],
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
