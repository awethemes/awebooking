<?php

namespace AweBooking\Admin\Settings;

use AweBooking\Support\WP_Data;

class General_Setting extends Abstract_Setting {
	/**
	 * {@inheritdoc}
	 */
	protected function setup() {
		$this->form_id  = 'general';
		$this->label    = esc_html__( 'General', 'awebooking' );
		$this->priority = 5;
	}

	/**
	 * Setup the fields.
	 *
	 * @return void
	 */
	public function setup_fields() {
		$this->add_field([
			'id'       => '__title_general',
			'type'     => 'title',
			'name'     => esc_html__( 'General', 'awebooking' ),
		]);

		$this->add_field([
			'id'       => 'enable_location',
			'type'     => 'abrs_toggle',
			'name'     => esc_html__( 'Multiple Hotels?', 'awebooking' ),
			'default'  => 'off',
		]);

		$this->add_field([
			'id'       => 'children_bookable',
			'type'     => 'abrs_toggle',
			'name'     => esc_html__( 'Children Bookable?', 'awebooking' ),
			'default'  => 'on',
		]);

		$this->add_field([
			'id'        => 'infants_bookable',
			'type'      => 'abrs_toggle',
			'name'      => esc_html__( 'Infants Bookable?', 'awebooking' ),
			'default'   => 'off',
		]);

		$this->add_field([
			'id'               => 'reservation_mode',
			'type'             => 'select',
			'name'             => esc_html__( 'Reservation Mode', 'awebooking' ),
			'default'          => 'multiple_room',
			'classes'          => 'with-selectize',
			'options'          => apply_filters( 'abrs_list_reservation_mode', [
				'single_room'   => esc_html__( 'Single Room', 'awebooking' ),
				'multiple_room' => esc_html__( 'Multiple Rooms', 'awebooking' ),
			]),
		]);

		$this->add_field([
			'id'      => 'measure_unit',
			'type'    => 'select',
			'section' => 'general',
			'name'    => esc_html__( 'Measurement Unit', 'awebooking' ),
			'default' => 'm2',
			'classes' => 'with-selectize',
			'options' => apply_filters( 'abrs_measure_units', [
				'm2'  => esc_html__( 'Square Meters', 'awebooking' ),
				'ft2' => esc_html__( 'Square Feet', 'awebooking' ),
			] ),
		]);

		// Pages settings.
		$all_pages_cb = WP_Data::cb( 'pages', [ 'post_status' => 'publish' ] );

		$this->add_field([
			'id'    => '__display_title',
			'type'  => 'title',
			'name'  => esc_html__( 'Pages', 'awebooking' ),
		]);

		$this->add_field([
			'id'               => 'page_check_availability',
			'type'             => 'select',
			'name'             => esc_html__( 'Availability Results', 'awebooking' ),
			'options_cb'       => $all_pages_cb,
			'sanitization_cb'  => 'absint',
			'classes'          => 'with-selectize',
			'show_option_none' => '---',
			'after'            => $this->get_external_link_cb(),
		]);

		$this->add_field([
			'id'               => 'page_checkout',
			'type'             => 'select',
			'name'             => esc_html__( 'Checkout Page', 'awebooking' ),
			'options_cb'       => $all_pages_cb,
			'sanitization_cb'  => 'absint',
			'classes'          => 'with-selectize',
			'show_option_none' => '---',
			'after'            => $this->get_external_link_cb(),
		]);

		$this->add_field([
			'id'               => 'page_terms',
			'type'             => 'select',
			'name'             => esc_html__( 'Terms and Conditions', 'awebooking' ),
			'options_cb'       => $all_pages_cb,
			'sanitization_cb'  => 'absint',
			'classes'          => 'with-selectize',
			'show_option_none' => '---',
		]);

		// Currency options.
		$this->add_field([
			'id'   => '__title_currency',
			'type' => 'title',
			'name' => esc_html__( 'Currency Options', 'awebooking' ),
		]);

		$this->add_field([
			'id'         => 'currency',
			'type'       => 'select',
			'name'       => esc_html__( 'Currency', 'awebooking' ),
			'default'    => 'USD',
			'options_cb' => 'abrs_list_dropdown_currencies',
			'classes'    => 'with-selectize',
		]);

		$this->add_field([
			'id'      => 'currency_position',
			'type'    => 'select',
			'name'    => esc_html__( 'Currency Position', 'awebooking' ),
			'default' => 'left',
			'classes' => 'with-selectize',
			'options' => [
				'left'        => esc_html__( 'Left', 'awebooking' ),
				'right'       => esc_html__( 'Right', 'awebooking' ),
				'left_space'  => esc_html__( 'Left with space', 'awebooking' ),
				'right_space' => esc_html__( 'Right with space', 'awebooking' ),
			],
		]);

		$this->add_field([
			'type'            => 'text_small',
			'id'              => 'price_thousand_separator',
			'name'            => esc_html__( 'Thousand Separator', 'awebooking' ),
			'default'         => ',',
			'sanitization_cb' => 'abrs_sanitize_text',
		]);

		$this->add_field([
			'type'            => 'text_small',
			'id'              => 'price_decimal_separator',
			'name'            => esc_html__( 'Decimal Separator', 'awebooking' ),
			'default'         => '.',
			'sanitization_cb' => 'abrs_sanitize_text',
		]);

		$this->add_field([
			'type'            => 'text_small',
			'id'              => 'price_number_decimals',
			'name'            => esc_html__( 'Number of Decimals', 'awebooking' ),
			'default'         => '2',
			'sanitization_cb' => 'absint',
			'attributes'      => [
				'min'  => 0,
				'step' => 1,
				'type' => 'number',
			],
		]);

		$this->add_field([
			'id'    => '__admin_calendar',
			'type'  => 'title',
			'name'  => esc_html__( 'Admin Calendar', 'awebooking' ),
		]);

		$this->add_field([
			'id'              => 'scheduler_display_duration',
			'type'            => 'select',
			'name'            => esc_html__( 'Calendar Duration', 'awebooking' ),
			'default'         => 30,
			'sanitization_cb' => 'absint',
			'classes'         => 'with-selectize',
			'options'         => [
				14  => esc_html__( '2 Weeks', 'awebooking' ),
				30  => esc_html__( '1 Month', 'awebooking' ),
				60  => esc_html__( '2 Months', 'awebooking' ),
				90  => esc_html__( '3 Months', 'awebooking' ),
				120 => esc_html__( '4 Months', 'awebooking' ),
			],
		]);
	}

	/**
	 * Gets the external link callback.
	 *
	 * @return \Closure
	 */
	protected function get_external_link_cb() {
		/**
		 * Prints the external link a select "page" field.
		 *
		 * @param  array       $args  The field args.
		 * @param  \CMB2_Field $field The field object.
		 * @return string
		 */
		return function ( $args, $field ) {
			$page_id = $field->escaped_value();

			if ( ! $page_id ) {
				return '';
			}

			return '<a href="' . esc_url( get_edit_post_link( $page_id ) ) . '" target="_blank"><span class="dashicons dashicons-external"></span></a>';
		};
	}
}
