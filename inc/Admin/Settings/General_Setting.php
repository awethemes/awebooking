<?php
namespace AweBooking\Admin\Settings;

use AweBooking\AweBooking;

class General_Setting extends Setting_Abstract {
	/**
	 * Register settings.
	 *
	 * @return void
	 */
	public function register() {
		$section = $this->settings->add_section( 'general', [
			'title' => esc_html__( 'General', 'awebooking' ),
			'priority' => 10,
		]);

		$section->add_field( array(
			'id'   => '__general_system__',
			'type' => 'title',
			'name' => esc_html__( 'AweBooking General', 'awebooking' ),
			'priority' => 10,
		) );

		$section->add_field( array(
			'id'       => 'enable_location',
			'type'     => 'toggle',
			'name'     => esc_html__( 'Multi hotel location?', 'awebooking' ),
			'default'  => awebooking( 'setting' )->get_default( 'enable_location' ),
			'priority' => 10,
		) );

		$section->add_field( array(
			'id'       => 'location_default',
			'type'     => 'select',
			'name'     => esc_html__( 'Default location', 'awebooking' ),
			'description' => esc_html__( 'Select a default location.', 'awebooking' ),
			'options_cb'  => wp_data_callback( 'terms',  array(
				'taxonomy'   => AweBooking::HOTEL_LOCATION,
				'hide_empty' => false,
			)),
			'validate' => 'integer',
			'deps'     => array( 'enable_location', '==', true ),
			'priority' => 15,
		) );

		$section->add_field( array(
			'id'   => '__general_currency__',
			'type' => 'title',
			'name' => esc_html__( 'Currency Options', 'awebooking' ),
			'priority' => 25,
		) );

		$section->add_field( array(
			'id'       => 'currency',
			'type'     => 'select',
			'name'     => esc_html__( 'Currency', 'awebooking' ),
			'default' => awebooking( 'setting' )->get_default( 'currency' ),
			'options_cb'  => function() {
				return awebooking( 'currency_manager' )->get_for_dropdown( '%name (%symbol)' );
			},
			'priority' => 25,
		) );

		$section->add_field( array(
			'id'       => 'currency_position',
			'type'     => 'select',
			'name'     => esc_html__( 'Currency position', 'awebooking' ),
			// 'desc'     => esc_html__( 'Controls the position of the currency symbol.', 'awebooking' ),
			'default'  => awebooking( 'setting' )->get_default( 'currency_position' ),
			'validate' => 'required',
			'options'  => awebooking( 'setting' )->get_currency_positions(),
			'priority' => 30,
		) );

		$section->add_field( array(
			'type'     => 'text_small',
			'id'       => 'price_thousand_separator',
			'name'     => esc_html__( 'Thousand separator', 'awebooking' ),
			// 'desc'     => esc_html__( 'Sets the thousand separator of displayed prices.', 'awebooking' ),
			'default'  => awebooking( 'setting' )->get_default( 'price_thousand_separator' ),
			'validate' => 'required',
			'priority' => 35,
		) );

		$section->add_field( array(
			'type'     => 'text_small',
			'id'       => 'price_decimal_separator',
			'name'     => esc_html__( 'Decimal separator', 'awebooking' ),
			// 'desc'     => esc_html__( 'Sets the decimal separator of displayed prices.', 'awebooking' ),
			'default'  => awebooking( 'setting' )->get_default( 'price_decimal_separator' ),
			'validate' => 'required',
			'priority' => 40,
		) );

		$section->add_field( array(
			'type'       => 'text_small',
			'id'         => 'price_number_decimals',
			'name'       => esc_html__( 'Number of decimals', 'awebooking' ),
			'default'    => awebooking( 'setting' )->get_default( 'price_number_decimals' ),
			'validate'   => 'required|integer|min:0',
			'attributes' => array(
				'min'  => 0,
				'type' => 'number',
			),
			'priority' => 45,
		) );
	}
}
