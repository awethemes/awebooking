<?php
namespace AweBooking\Admin\Settings;

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

		// Address.
		$this->add_field([
			'id'       => '__hotel_address',
			'type'     => 'title',
			'name'     => esc_html__( 'Hotel & Address', 'awebooking' ),
			'desc'     => esc_html__( 'This is where your hotel is located. Tax rates will use this address.', 'awebooking' ),
		]);

		$this->add_field([
			'id'              => 'hotel_name',
			'type'            => 'text',
			'name'            => esc_html__( 'Name', 'awebooking' ),
			'default'         => get_bloginfo( 'name' ),
			'required'        => true,
			'sanitization_cb' => 'abrs_sanitize_text',
		]);

		$this->add_field([
			'id'              => 'star_rating',
			'type'            => 'select',
			'name'            => esc_html__( 'Star Rating', 'awebooking' ),
			'classes'         => 'with-selectize',
			'options'         => [
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
			'name'            => esc_html__( 'Postcode / ZIP', 'awebooking' ),
			'desc'            => esc_html__( 'The postal code, if any, in which your hotel is located.', 'awebooking' ),
			'id'              => 'hotel_postcode',
			'type'            => 'text',
			'sanitization_cb' => 'abrs_sanitize_text',
			'tooltip'         => true,
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
	}
}
