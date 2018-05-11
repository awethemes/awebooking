<?php
namespace AweBooking\Frontend\Checkout;

use AweBooking\Component\Form\Form_Builder;

class Form_Controls extends Form_Builder {
	/**
	 * Constructor.
	 *
	 * @param mixed $object The object form data.
	 */
	public function __construct( $object = null ) {
		parent::__construct( 'checkout-controls', $object, 'static' );
	}

	/**
	 * Gets the mandatory controls.
	 *
	 * @return array
	 */
	public function get_mandatory() {
		return apply_filters( 'awebooking/checkout_mandatory_controls', [ 'customer_first_name', 'customer_email' ] );
	}

	/**
	 * Setup the fields.
	 *
	 * @return void
	 */
	protected function setup_fields() {
		$additionals = $this->add_section( 'additionals' );

		$additionals->add_field([
			'id'               => 'arrival_time',
			'type'             => 'select',
			'name'             => esc_html__( 'Estimated time of arrival', 'awebooking' ),
			'options_cb'       => 'abrs_list_hours',
			'classes'          => 'with-selectize',
			'show_option_none' => esc_html__( 'I don\'t know', 'awebooking' ),
			'sanitization_cb'  => 'absint',
		]);

		$additionals->add_field([
			'id'               => 'customer_note',
			'type'             => 'textarea',
			'name'             => esc_html__( 'Special requests', 'awebooking' ),
			'sanitization_cb'  => 'sanitize_textarea_field',
		]);

		$customer = $this->add_section( 'customer' );

		$customer->add_field([
			'id'               => 'customer_title',
			'type'             => 'select',
			'name'             => esc_html__( 'Title', 'awebooking' ),
			'options_cb'       => 'abrs_list_common_titles',
			'classes'          => 'with-selectize',
			'show_option_none' => '---',
		]);

		$customer->add_field([
			'id'           => 'customer_first_name',
			'type'         => 'text',
			'name'         => esc_html__( 'First name', 'awebooking' ),
			'required'     => true,
			'col-half'     => true,
			'attributes'   => [
				'autofocus'    => true,
				'autocomplete' => 'given-name',
			],
		]);

		$customer->add_field([
			'id'           => 'customer_last_name',
			'type'         => 'text',
			'name'         => esc_html__( 'Last name', 'awebooking' ),
			'required'     => true,
			'attributes'   => [ 'autocomplete' => 'family-name' ],
			'col-half'     => true,
		]);

		$customer->add_field([
			'id'           => 'customer_company',
			'type'         => 'text',
			'name'         => esc_html__( 'Company name', 'awebooking' ),
			'attributes'   => [ 'autocomplete' => 'organization' ],
		]);

		$customer->add_field([
			'id'           => 'customer_address',
			'type'         => 'text',
			'name'         => esc_html__( 'Street address', 'awebooking' ),
			'required'     => true,
			'col-half'     => true,
			'attributes'   => [
				'autocomplete' => 'address-line1',
				'placeholder'  => esc_html__( 'House number and street name', 'awebooking' ),
			],
		]);

		$customer->add_field([
			'id'           => 'customer_address_2',
			'name'         => esc_html__( 'Address 2', 'awebooking' ),
			'type'         => 'text',
			'col-half'     => true,
			'attributes'   => [
				'placeholder'  => esc_html__( 'Apartment, suite, unit etc. (optional)', 'awebooking' ),
				'autocomplete' => 'address-line2',
			],
		]);

		$customer->add_field([
			'id'           => 'customer_city',
			'type'         => 'text',
			'name'         => esc_html__( 'Town / City', 'awebooking' ),
			'required'     => true,
			'attributes'   => [ 'autocomplete' => 'address-level2' ],
			'col-half'     => true,
		]);

		$customer->add_field([
			'id'           => 'customer_state',
			'type'         => 'text',
			'name'         => esc_html__( 'State / County', 'awebooking' ),
			'required'     => true,
			'attributes'   => [ 'autocomplete' => 'address-level1' ],
			'col-half'     => true,
		]);

		$customer->add_field([
			'id'               => 'customer_country',
			'type'             => 'select',
			'name'             => esc_html__( 'Country', 'awebooking' ),
			'classes'          => 'with-selectize',
			'options_cb'       => 'abrs_list_countries',
			'show_option_none' => '---',
			'attributes'       => [ 'autocomplete' => 'country' ],
			'required'         => true,
			'col-half'         => true,
		]);

		$customer->add_field([
			'id'           => 'customer_postal_code',
			'type'         => 'text',
			'name'         => esc_html__( 'Postcode / ZIP', 'awebooking' ),
			'required'     => true,
			'attributes'   => [ 'autocomplete' => 'postal-code' ],
			'col-half'     => true,
		]);

		$customer->add_field([
			'id'           => 'customer_phone',
			'type'         => 'text',
			'name'         => esc_html__( 'Phone number', 'awebooking' ),
			'required'     => true,
			'attributes'   => [ 'autocomplete' => 'tel' ],
			'col-half'     => true,
		]);

		$customer->add_field([
			'id'               => 'customer_email',
			'type'             => 'text',
			'name'             => esc_html__( 'Email address', 'awebooking' ),
			'required'         => true,
			'sanitization_cb'  => 'sanitize_email',
			'col-half'         => true,
			'attributes'       => [
				'type'         => 'email',
				'autocomplete' => 'email',
			],
		]);
	}
}
