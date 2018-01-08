<?php
namespace AweBooking\Admin\Settings;

use AweBooking\Admin\Admin_Settings;

class Display_Setting extends Abstract_Setting {
	/**
	 * {@inheritdoc}
	 */
	public function registers( Admin_Settings $settings ) {
		$display = $settings->add_section( 'display', [
			'title' => esc_html__( 'Display', 'awebooking' ),
			'capability' => 'manage_awebooking',
		]);

		$display->add_field( array(
			'id'   => '__display_pages__',
			'type' => 'title',
			'name' => esc_html__( 'AweBooking Pages', 'awebooking' ),
			'description' => esc_html__( 'These pages need to be set so that AweBooking knows where to send users to handle.', 'awebooking' ),
		) );

		$display->add_field( array(
			'id'           => 'page_check_availability',
			'type'         => 'select',
			'name'         => esc_html__( 'Check Availability', 'awebooking' ),
			'description'  => esc_html__( 'Selected page to display check availability form.', 'awebooking' ),
			'default'      => awebooking( 'setting' )->get_default( 'page_check_availability' ),
			'options_cb'   => wp_data_callback( 'pages', array( 'post_status' => 'publish' ) ),
			'validate'     => 'integer',
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
			'show_option_none' => true,
		) );

		$display->add_field( array(
			'id'       => '__display_check_availability__',
			'type'     => 'title',
			'name'     => esc_html__( 'Check availability', 'awebooking' ),
		) );

		$display->add_field( array(
			'id'         => 'check_availability_max_adults',
			'type'       => 'text_small',
			'attributes' => array( 'type' => 'number' ),
			'name'       => esc_html__( 'Max adults', 'awebooking' ),
			'default'    => awebooking( 'setting' )->get_default( 'check_availability_max_adults' ),
			'validate'   => 'integer|min:1',
			'sanitization_cb' => 'absint',
		) );

		if ( awebooking( 'setting' )->get_children_bookable() ) {
			$display->add_field( array(
				'id'         => 'check_availability_max_children',
				'type'       => 'text_small',
				'attributes' => array( 'type' => 'number' ),
				'name'       => esc_html__( 'Max children', 'awebooking' ),
				'default'    => awebooking( 'setting' )->get_default( 'check_availability_max_children' ),
				'validate'   => 'integer|min:0',
				'sanitization_cb' => 'absint',
			) );
		}

		if ( awebooking( 'setting' )->get_infants_bookable() ) {
			$display->add_field( array(
				'id'         => 'check_availability_max_infants',
				'type'       => 'text_small',
				'attributes' => array( 'type' => 'number' ),
				'name'       => esc_html__( 'Max infants', 'awebooking' ),
				'default'    => awebooking( 'setting' )->get_default( 'check_availability_max_infants' ),
				'validate'   => 'integer|min:0',
				'sanitization_cb' => 'absint',
			) );
		}

		$display->add_field( array(
			'id'   => 'page_for_check_availability',
			'type' => 'title',
			'name' => esc_html__( 'Page for check availability ', 'awebooking' ),
		) );

		$display->add_field( array(
			'id'       => 'showing_price',
			'type'     => 'select',
			'name'     => esc_html__( 'Showing price', 'awebooking' ),
			'description' => esc_html__( 'Selected a type of price to showing in the checking availability page.', 'awebooking' ),
			'default'  => awebooking( 'setting' )->get_default( 'showing_price' ),
			'options'  => array(
				'start_price'    => esc_html__( 'Starting price', 'awebooking' ),
				'average_price'  => esc_html__( 'Average price', 'awebooking' ),
				'total_price'    => esc_html__( 'Total price', 'awebooking' ),
			),
			'show_option_none' => false,
		) );
	}
}
