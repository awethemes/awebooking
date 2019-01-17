<?php

namespace AweBooking\Checkout;

use AweBooking\Support\Fluent;
use AweBooking\Component\Form\Form;

class Form_Controls extends Form {
	/**
	 * Constructor.
	 *
	 * @param mixed $object The object form data.
	 */
	public function __construct( $object = null ) {
		parent::__construct( 'checkout-controls', is_null( $object ) ? new Fluent : $object, 'static' );
	}

	/**
	 * Filter enabled controls only.
	 *
	 * @return $this
	 */
	public function enabled() {
		foreach ( $this->get_disable_controls() as $key ) {
			$this->remove_field( $key );
		}

		return $this;
	}

	/**
	 * Gets the enable controls.
	 *
	 * @return array
	 */
	public function get_enable_controls() {
		if ( $_controls = apply_filters( 'abrs_checkout_pre_enable_controls', null, $this ) ) {
			return $_controls;
		}

		$ids = array_column( $this->prop( 'fields' ), 'id' );
		$data_controls = (array) abrs_get_option( 'list_checkout_controls', [] );

		if ( empty( $data_controls ) ) {
			return $ids;
		}

		$controls = array_filter( $ids, function( $key ) use ( $data_controls ) {
			if ( in_array( $key, $this->get_mandatory_controls() ) ) {
				return true;
			}

			return array_key_exists( $key, $data_controls ) && $data_controls[ $key ];
		});

		return apply_filters( 'abrs_checkout_enable_controls', $controls, $this );
	}

	/**
	 * Gets the disable controls.
	 *
	 * @return array
	 */
	public function get_disable_controls() {
		$ids = array_column( $this->prop( 'fields' ), 'id' );

		return array_diff( $ids, $this->get_enable_controls() );
	}

	/**
	 * Gets the mandatory controls.
	 *
	 * @return array
	 */
	public function get_mandatory_controls() {
		return apply_filters( 'abrs_checkout_mandatory_controls', [ 'customer_first_name', 'customer_email' ] );
	}

	/**
	 * Setup the fields.
	 *
	 * @return void
	 */
	protected function setup_fields() {
		$customer = $this->add_section( 'customer', [
			'title'    => esc_html__( 'Customer Details', 'awebooking' ),
			'priority' => 1,
		]);

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
			'validate'         => 'email',
			'sanitization_cb'  => 'sanitize_email',
			'col-half'         => true,
			'attributes'       => [
				'type'         => 'email',
				'autocomplete' => 'email',
			],
		]);

		$additionals = $this->add_section( 'additionals', [
			'title'    => esc_html__( 'Additionals', 'awebooking' ),
			'priority' => 1,
		]);

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
			'attributes' => [
				'rows' => 3,
			],
		]);

		do_action( 'abrs_checkout_setup_controls', $this );
	}
}
