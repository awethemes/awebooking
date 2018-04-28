<?php
namespace AweBooking\Admin\Settings;

class Hotel_Setting extends Abstract_Setting {
	/**
	 * The setting ID.
	 *
	 * @var string
	 */
	protected $form_id = 'hotel';

	/**
	 * Get the setting label.
	 *
	 * @return string
	 */
	public function get_label() {
		return esc_html__( 'Hotel', 'awebooking' );
	}

	/**
	 * Setup the fields.
	 *
	 * @return void
	 */
	public function setup_fields() {
		$this->add_field([
			'id'       => '__hotel_title',
			'type'     => 'title',
			'name'     => esc_html__( 'Hotel & Address', 'awebooking' ),
			'desc'     => esc_html__( 'This is where your hotel is located. Tax rates will use this address.', 'awebooking' ),
		]);

		// Prevent in some case we have a value called like: "awebooking".
		$hotel_name = get_bloginfo( 'name' );
		if ( function_exists( $hotel_name ) ) {
			$hotel_name = sprintf( '%s Hotel', $hotel_name );
		}

		$this->add_field([
			'id'              => 'hotel_name',
			'type'            => 'text',
			'name'            => esc_html__( 'Name', 'awebooking' ),
			'default'         => $hotel_name,
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
	}
}
