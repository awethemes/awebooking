<?php
namespace AweBooking\Admin\Forms;

use AweBooking\Constants;

class New_Reservation_Form extends Form_Abstract {
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
		$options = $this->get_options();

		$this->add_field([
			'id'          => 'reservation_source',
			'type'        => 'select',
			'name'        => esc_html__( 'Select source', 'awebooking' ),
			'validate'    => 'required',
			'options'     => $options,
		]);

		$this->add_field([
			'id'          => 'check_in_out',
			'type'        => 'date_range',
			'name'        => esc_html__( 'Check-In and Check-Out', 'awebooking' ),
			'validate'    => 'date_period',
			'attributes'  => [ 'placeholder' => Constants::DATE_FORMAT ],
			'date_format' => Constants::DATE_FORMAT,
		]);
	}

	protected function get_options() {
		$a = awebooking( 'reservation_sources' )->to_collection()
			->pluck( 'label', 'uid' )
			->toArray();

		return $a;
	}
}
