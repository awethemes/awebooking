<?php

namespace AweBooking\Admin\Forms;

use AweBooking\Component\Form\Form;
use AweBooking\Model\Booking\Room_Item;

class Edit_Booking_Room_Form extends Form {
	/**
	 * Constructor.
	 *
	 * @param \AweBooking\Model\Booking\Room_Item $model The model.
	 */
	public function __construct( Room_Item $model ) {
		parent::__construct( 'booking-room', $model );
	}

	/**
	 * {@inheritdoc}
	 */
	protected function setup_fields() {
		$booking       = abrs_get_booking( $this->object_id->get( 'booking_id' ) );
		$max_occupancy = (int) get_post_meta( $this->object_id->get( 'room_type_id' ), '_maximum_occupancy', true );

		$this->add_field( [
			'id'          => 'change_timespan',
			'type'        => 'abrs_dates',
			'name'        => esc_html__( 'Change timespan to:', 'awebooking' ),
			'default'     => [ $this->object_id->get( 'check_in' ), $this->object_id->get( 'check_out' ) ],
			'input_names' => [ 'change_check_in', 'change_check_out' ],
		] );

		$this->add_field( [
			'id'              => 'adults',
			'type'            => 'select',
			'name'            => esc_html__( 'Adults', 'awebooking' ),
			'default'         => 1,
			'options'         => array_combine( $r = range( 1, $max_occupancy ), $r ),
			'sanitization_cb' => 'absint',
		] );

		if ( abrs_children_bookable() ) {
			$this->add_field( [
				'id'              => 'children',
				'type'            => 'select',
				'name'            => esc_html__( 'Children', 'awebooking' ),
				'default'         => 0,
				'options'         => array_combine( $r = range( 0, $max_occupancy ), $r ),
				'sanitization_cb' => 'absint',
			] );
		}

		if ( abrs_infants_bookable() ) {
			$this->add_field( [
				'id'              => 'infants',
				'type'            => 'select',
				'name'            => esc_html__( 'Infants', 'awebooking' ),
				'options'         => array_combine( $r = range( 0, $max_occupancy ), $r ),
				'default'         => 0,
				'sanitization_cb' => 'absint',
			] );
		}

		$this->add_field( [
			'id'       => 'subtotal',
			'type'     => 'abrs_amount',
			'name'     => esc_html__( 'Subtotal (pre-discount)', 'awebooking' ),
			'currency' => abrs_optional( $booking )->get( 'currency' ),
		] );

		$this->add_field( [
			'id'       => 'total',
			'type'     => 'abrs_amount',
			'name'     => esc_html__( 'Total', 'awebooking' ),
			'currency' => abrs_optional( $booking )->get( 'currency' ),
		] );

		$this->add_field( [
			'id'               => 'swap_to_room',
			'type'             => 'select',
			'show_option_none' => esc_html__( 'Select room', 'awebooking' ),
			'name'             => esc_html__( 'Swap To', 'awebooking' ),
		] );
	}
}
