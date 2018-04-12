<?php
namespace AweBooking\Admin\Forms;

use AweBooking\Support\Carbonate;
use AweBooking\Component\Form\Form_Builder;

class Search_Reservation_Form extends Form_Builder {
	/**
	 * Constructor.
	 */
	public function __construct() {
		parent::__construct( 'search_reservation', 0, 'static' );
	}

	/**
	 * {@inheritdoc}
	 */
	protected function setup_fields() {
		$this->add_field([
			'id'          => 'reservation_source',
			'type'        => 'select',
			'name'        => esc_html__( 'Select Source', 'awebooking' ),
			'options_cb'  => '',
		]);

		$this->add_field([
			'id'          => 'date',
			'type'        => 'abrs_dates',
			'name'        => esc_html__( 'Check-In and Check-Out', 'awebooking' ),
			'default'     => [ Carbonate::today()->format( 'Y-m-d' ), Carbonate::tomorrow()->format( 'Y-m-d' ) ],
		]);
	}
}
