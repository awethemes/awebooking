<?php

namespace AweBooking\Admin\Forms;

use AweBooking\Support\Fluent;
use AweBooking\Component\Form\Form;

class Hotel_Information_Form extends Form {
	/**
	 * Constructor.
	 *
	 * @param mixed $object The hotel object data.
	 */
	public function __construct( $object = null ) {
		parent::__construct( 'hotel-information', is_null( $object ) ? new Fluent : $object, 'static' );
	}

	/**
	 * {@inheritdoc}
	 */
	protected function setup_fields() {
		$this->add_field([
			'id'          => 'hotel_check_in',
			'type'        => 'text_time',
			'name'        => esc_html__( 'Check-in time', 'awebooking' ),
			'desc'        => esc_html__( 'Guest arrival time', 'awebooking' ),
			'tooltip'     => true,
			'default'     => '12:00',
			'time_format' => 'H:i',
		]);

		$this->add_field([
			'id'          => 'hotel_check_out',
			'type'        => 'text_time',
			'name'        => esc_html__( 'Check-out time', 'awebooking' ),
			'desc'        => esc_html__( 'Guest departure time.', 'awebooking' ),
			'tooltip'     => true,
			'time_format' => 'H:i',
		]);

		$this->add_field([
			'id'      => 'hotel_star_rating',
			'type'    => 'select',
			'name'    => esc_html__( 'Star Rating', 'awebooking' ),
			'classes' => 'with-selectize',
			'desc'    => esc_html__( 'The hotel rating.', 'awebooking' ),
			'tooltip' => true,
			'options' => [
				''  => esc_html__( 'N/A', 'awebooking' ),
				'1' => '1&nbsp;&#9733;',
				'2' => '2&nbsp;&#9733;&#9733;',
				'3' => '3&nbsp;&#9733;&#9733;&#9733;',
				'4' => '4&nbsp;&#9733;&#9733;&#9733;&#9733;',
				'5' => '5&nbsp;&#9733;&#9733;&#9733;&#9733;&#9733;',
			],
		]);

		$this->add_field([
			'id'              => 'hotel_address',
			'type'            => 'text',
			'name'            => esc_html__( 'Address Line', 'awebooking' ),
			'desc'            => esc_html__( 'The street address for your hotel location.', 'awebooking' ),
			'sanitization_cb' => 'abrs_sanitize_text',
			'tooltip'         => true,
		]);

		$this->add_field([
			'id'              => 'hotel_address_2',
			'type'            => 'text',
			'name'            => esc_html__( 'Address line 2', 'awebooking' ),
			'desc'            => esc_html__( 'An additional, optional address line for your hotel location.', 'awebooking' ),
			'sanitization_cb' => 'abrs_sanitize_text',
			'tooltip'         => true,
		]);

		$this->add_field([
			'id'              => 'hotel_city',
			'type'            => 'text',
			'name'            => esc_html__( 'City', 'awebooking' ),
			'desc'            => esc_html__( 'The city in which your hotel is located.', 'awebooking' ),
			'sanitization_cb' => 'abrs_sanitize_text',
			'tooltip'         => true,
		]);

		$this->add_field([
			'id'               => 'hotel_country',
			'type'             => 'select',
			'name'             => esc_html__( 'Country', 'awebooking' ),
			'desc'             => esc_html__( 'The country in which your hotel is located.', 'awebooking' ),
			'options_cb'       => 'abrs_list_countries',
			'classes'          => 'with-selectize',
			'show_option_none' => '---',
			'tooltip'          => true,
		]);

		$this->add_field([
			'id'              => 'hotel_postcode',
			'type'            => 'text',
			'name'            => esc_html__( 'Postcode / ZIP', 'awebooking' ),
			'desc'            => esc_html__( 'The postal code, if any, in which your hotel is located.', 'awebooking' ),
			'sanitization_cb' => 'abrs_sanitize_text',
			'tooltip'         => true,
		]);

		$this->add_field([
			'id'         => 'hotel_phone',
			'type'       => 'text',
			'name'       => esc_html__( 'Phone number', 'awebooking' ),
			'attributes' => [ 'autocomplete' => 'tel' ],
			'desc'       => esc_html__( 'The hotel telephone number.', 'awebooking' ),
			'tooltip'    => true,
		]);
	}
}
