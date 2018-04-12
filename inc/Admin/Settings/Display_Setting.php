<?php
namespace AweBooking\Admin\Settings;

use AweBooking\Support\WP_Data;
use AweBooking\Admin\Admin_Settings;

class Display_Setting extends Abstract_Setting {
	/**
	 * The setting ID.
	 *
	 * @var string
	 */
	protected $form_id = 'display';

	/**
	 * Get the setting label.
	 *
	 * @return string
	 */
	public function get_label() {
		return esc_html__( 'Display', 'awebooking' );
	}

	/**
	 * Setup the fields.
	 *
	 * @return void
	 */
	public function setup_fields() {
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
		]);

		$this->add_field([
			'id'               => 'page_booking',
			'type'             => 'select',
			'name'             => esc_html__( 'Confirm Booking', 'awebooking' ),
			'options_cb'       => $all_pages_cb,
			'sanitization_cb'  => 'absint',
			'classes'          => 'with-selectize',
			'show_option_none' => '---',
		]);

		$this->add_field([
			'id'               => 'page_checkout',
			'type'             => 'select',
			'name'             => esc_html__( 'Checkout Page', 'awebooking' ),
			'options_cb'       => $all_pages_cb,
			'sanitization_cb'  => 'absint',
			'classes'          => 'with-selectize',
			'show_option_none' => '---',
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
			'classes'         => 'with-selectize',
			'default'         => 30,
			'sanitization_cb' => 'absint',
			'options'         => [
				14  => esc_html__( '2 Weeks', 'awebooking' ),
				30  => esc_html__( '1 Month', 'awebooking' ),
				60  => esc_html__( '2 Months', 'awebooking' ),
				90  => esc_html__( '3 Months', 'awebooking' ),
				120 => esc_html__( '4 Months', 'awebooking' ),
			],
		]);
	}
}
