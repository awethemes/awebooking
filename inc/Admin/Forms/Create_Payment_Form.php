<?php
namespace AweBooking\Admin\Forms;

use AweBooking\Dropdown;
use AweBooking\Model\Booking;

class Create_Payment_Form extends Form_Abstract {
	/**
	 * The form ID.
	 *
	 * @var string
	 */
	protected $form_id = 'awebooking_new_reservation_source';

	/**
	 * The booking instance.
	 *
	 * @var \AweBooking\Model\Booking
	 */
	protected $booking;

	/**
	 * Constructor.
	 *
	 * @param \AweBooking\Model\Booking $booking The booking instance.
	 */
	public function __construct( Booking $booking ) {
		$this->booking = $booking;

		parent::__construct();
	}

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
			'before_row'  => $this->callback_before_amount(),
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
			'id'          => 'is_deposit',
			'type'        => 'toggle',
			'name'        => esc_html__( 'Deposit', 'awebooking' ),
			'desc'        => esc_html__( 'Is this deposit?', 'awebooking' ),
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

	/**
	 * Callback before print amout field.
	 *
	 * @return Closure
	 */
	protected function callback_before_amount() {
		return function() {
			$booking = $this->booking;

			$paid = $booking->get_paid();
			$balance_due = $booking->get_balance_due(); ?>

			<div class="cmb-row">
				<div class="cmb-th"><?php echo esc_html__( 'Total charge', 'awebooking' ); ?></div>

				<div class="cmb-td">
					<span class="awebooking-label"><?php $booking->format_money( $booking->get_total() ); ?></span>
				</div>
			</div>

			<div class="cmb-row">
				<div class="cmb-th"><?php echo esc_html__( 'Already paid', 'awebooking' ); ?></div>

				<div class="cmb-td">
					<span class="awebooking-label awebooking-label--<?php echo ( $paid->is_zero() ) ? 'danger' : 'success'; ?>"><?php $booking->format_money( $paid ); ?></span>
				</div>
			</div>

			<div class="cmb-row">
				<div class="cmb-th"><?php echo esc_html__( 'Balance Due', 'awebooking' ); ?></div>

				<div class="cmb-td">
					<span class="awebooking-label awebooking-label--<?php echo ( $balance_due->is_zero() ) ? 'success' : 'danger'; ?>"><?php $booking->format_money( $balance_due ); ?></span>
				</div>
			</div>
		<?php }; // @codingStandardsIgnoreLine
	}
}
