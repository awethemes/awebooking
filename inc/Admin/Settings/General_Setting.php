<?php
namespace AweBooking\Admin\Settings;

use AweBooking\Support\WP_Data;

class General_Setting extends Abstract_Setting {
	/**
	 * The setting ID.
	 *
	 * @var string
	 */
	protected $form_id = 'general';

	/**
	 * Get the setting label.
	 *
	 * @return string
	 */
	public function get_label() {
		return esc_html__( 'General', 'awebooking' );
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
			'default'   => 'on',
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

		// Currency options.
		$this->add_field([
			'id'   => '__title_currency',
			'type' => 'title',
			'name' => esc_html__( 'Currency Options', 'awebooking' ),
		]);

		$this->add_field([
			'id'          => 'currency',
			'type'        => 'select',
			'name'        => esc_html__( 'Currency', 'awebooking' ),
			'default'     => 'USD',
			'options_cb'  => 'abrs_list_dropdown_currencies',
			'classes'     => 'with-selectize',
		]);

		$this->add_field([
			'id'       => 'currency_position',
			'type'     => 'select',
			'name'     => esc_html__( 'Currency Position', 'awebooking' ),
			'default'  => 'left',
			'classes'  => 'with-selectize',
			'options'  => [
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
