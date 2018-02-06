<?php
namespace AweBooking\Admin\Forms;

class New_Tax_Form extends Form_Abstract {
	/**
	 * The form ID.
	 *
	 * @var string
	 */
	protected $form_id = 'awebooking_new_reservation_tax';

	/**
	 * {@inheritdoc}
	 */
	protected function fields() {
		$this->add_field([
			'id'          => 'type',
			'type'        => 'radio_inline',
			'name'        => esc_html__( 'Is this a tax or fee?', 'awebooking' ),
			'options'     => [
				'tax' => esc_html__( 'Tax', 'awebooking' ),
				'fee' => esc_html__( 'Fee', 'awebooking' ),
			],
			'default'     => 'tax',
			'validate'    => 'required',
		]);

		$this->add_field([
			'id'          => 'name',
			'type'        => 'text_medium',
			'name'        => esc_html__( 'Name', 'awebooking' ),
			'validate'    => 'required',
			'sanitization_cb' => 'sanitize_text_field',
		]);

		$this->add_field([
			'id'          => 'code',
			'type'        => 'text_small',
			'name'        => esc_html__( 'Code', 'awebooking' ),
			'validate'    => 'required',
			'sanitization_cb' => 'sanitize_text_field',
		]);

		$this->add_field([
			'id'          => 'category',
			'type'        => 'radio',
			'name'        => esc_html__( 'How is this tax / fee calculated?', 'awebooking' ),
			'options'     => [
				'exclusive' => esc_html__( 'Exclusive: This tax / fee is not included in the rate.', 'awebooking' ),
				'inclusive' => esc_html__( 'Inclusive: This tax / fee is included in the rate.', 'awebooking' ),
			],
			'default'     => 'exclusive',
			'validate'    => 'required',
		]);

		$this->add_field([
			'id'          => 'amount_type',
			'type'        => 'radio',
			'name'        => esc_html__( 'Amount type', 'awebooking' ),
			'options'     => [
				'percentage' => esc_html__( 'Percentage', 'awebooking' ),
				'fixed'      => esc_html__( 'Fixed', 'awebooking' ),
			],
			'default'     => 'percentage',
			'validate'    => 'required',
		]);

		$this->add_field([
			'id'          => 'amount',
			'type'        => 'text_small',
			'name'        => esc_html__( 'Amount', 'awebooking' ),
			'validate'    => 'required',
			'sanitization_cb' => 'sanitize_text_field',
		]);
	}
}
