<?php
namespace AweBooking\Admin\Forms;

use AweBooking\Constants;

class Set_Price_Form extends Form_Abstract {
	/**
	 * Form ID.
	 *
	 * @var string
	 */
	protected $form_id = 'awebooking_admin_reservation_from';

	/**
	 * {@inheritdoc}
	 */
	protected function fields() {
		$this->add_field([
			'id'          => 'set_amount_period',
			'type'        => 'date_range',
			'name'        => esc_html__( 'Period', 'awebooking' ),
			'validate'    => 'date_period',
			'attributes'  => [ 'placeholder' => Constants::DATE_FORMAT ],
			'date_format' => Constants::DATE_FORMAT,
		]);

		$this->add_field([
			'id'              => 'set_amount',
			'type'            => 'text_small',
			'name'            => esc_html__( 'Total price', 'awebooking' ),
			'validate'        => 'required|price',
			'sanitization_cb' => 'awebooking_sanitize_price',
		]);
	}
}
