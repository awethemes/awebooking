<?php
namespace AweBooking\Admin\Settings;

use AweBooking\AweBooking;

class Display_Setting extends Setting_Abstract {
	/**
	 * Register settings.
	 *
	 * @return void
	 */
	public function register() {
		$display = $this->settings->add_section( 'display', [
			'title' => esc_html__( 'Display', 'awebooking' ),
			'priority' => 20,
		]);

		$display->add_field( array(
			'id'   => '__display_pages__',
			'type' => 'title',
			'name' => esc_html__( 'AweBooking Pages', 'awebooking' ),
			'description' => esc_html__( 'These pages need to be set so that AweBooking knows where to send users to handle.', 'awebooking' ),
			'priority' => 10,
		) );

		$display->add_field( array(
			'id'           => 'page_check_availability',
			'type'         => 'select',
			'name'         => esc_html__( 'Check Availability', 'awebooking' ),
			'description'  => esc_html__( 'Selected page to display check availability form.', 'awebooking' ),
			'default'      => awebooking( 'setting' )->get_default( 'page_check_availability' ),
			'options_cb'   => wp_data_callback( 'pages', array( 'post_status' => 'publish' ) ),
			'validate'     => 'integer',
			'priority'     => 10,
			'show_option_none' => true,
		) );

		$display->add_field( array(
			'id'          => 'page_booking',
			'type'        => 'select',
			'name'        => esc_html__( 'Booking Informations', 'awebooking' ),
			'description' => esc_html__( 'Selected page to display booking informations.', 'awebooking' ),
			'default'     => awebooking( 'setting' )->get_default( 'page_booking' ),
			'options_cb'  => wp_data_callback( 'pages', array( 'post_status' => 'publish' ) ),
			'validate'    => 'integer',
			'priority'    => 15,
			'show_option_none' => true,
		) );

		$display->add_field( array(
			'id'          => 'page_checkout',
			'type'        => 'select',
			'name'        => esc_html__( 'Confirm Booking', 'awebooking' ),
			'description' => esc_html__( 'Selected page to display checkout informations.', 'awebooking' ),
			'default'     => awebooking( 'setting' )->get_default( 'page_checkout' ),
			'options_cb'  => wp_data_callback( 'pages', array( 'post_status' => 'publish' ) ),
			'validate'    => 'integer',
			'priority'    => 20,
			'show_option_none' => true,
		) );

		$display->add_field( array(
			'id'       => '__display_check_availability__',
			'type'     => 'title',
			'name'     => esc_html__( 'Check availability', 'awebooking' ),
			'priority' => 25,
		) );

		$display->add_field( array(
			'id'         => 'check_availability_max_adults',
			'type'       => 'text_small',
			'attributes' => array( 'type' => 'number' ),
			'name'       => esc_html__( 'Max adults', 'awebooking' ),
			'default'    => awebooking( 'setting' )->get_default( 'check_availability_max_adults' ),
			'validate'   => 'integer|min:1',
			'priority'   => 30,
			'sanitization_cb' => 'absint',
		) );

		$display->add_field( array(
			'id'         => 'check_availability_max_children',
			'type'       => 'text_small',
			'attributes' => array( 'type' => 'number' ),
			'name'       => esc_html__( 'Max children', 'awebooking' ),
			'default'    => awebooking( 'setting' )->get_default( 'check_availability_max_children' ),
			'validate'   => 'integer|min:0',
			'priority'   => 35,
			'sanitization_cb' => 'absint',
		) );

		$display->add_field( array(
			'id'   => 'page_for_check_availability',
			'type' => 'title',
			'name' => esc_html__( 'Page for check availability ', 'awebooking' ),
			'priority' => 40,
		) );

		$display->add_field( array(
			'id'       => 'showing_price',
			'type'     => 'select',
			'name'     => esc_html__( 'Showing price', 'awebooking' ),
			'description' => esc_html__( 'Selected a type of price to showing in the checking availability page.', 'awebooking' ),
			'default'  => awebooking( 'setting' )->get_default( 'showing_price' ),
			'options'	 => array(
				'start_price'	 => esc_html__( 'Starting price', 'awebooking' ),
				'average_price'  => esc_html__( 'Average price', 'awebooking' ),
				'total_price' 	 => esc_html__( 'Total price', 'awebooking' ),
			),
			'show_option_none' => false,
			'priority' => 45,
		) );
	}
}
