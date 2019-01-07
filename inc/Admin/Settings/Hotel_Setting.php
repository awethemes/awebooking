<?php

namespace AweBooking\Admin\Settings;

use WPLibs\Http\Request;
use AweBooking\Model\Hotel;
use AweBooking\Support\WP_Data;
use AweBooking\Admin\Forms\Hotel_Information_Form;

class Hotel_Setting extends Abstract_Setting {
	/**
	 * {@inheritdoc}
	 */
	protected function setup() {
		$this->form_id  = 'hotel';
		$this->label    = esc_html__( 'Hotel', 'awebooking' );
		$this->priority = 10;
	}

	/**
	 * {@inheritdoc}
	 */
	public function setup_fields() {
		$this->add_field([
			'id'       => '__hotel_title',
			'type'     => 'title',
			'name'     => esc_html__( 'Hotel & Address', 'awebooking' ),
			'desc'     => esc_html__( 'This is where your hotel is located. Tax rates (if applicable) will use this address.', 'awebooking' ),
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
			'desc'            => esc_html__( 'The primary hotel name.', 'awebooking' ),
			'tooltip'         => true,
		]);

		if ( ! abrs_multiple_hotels() ) {
			foreach ( ( new Hotel_Information_Form )->prop( 'fields' ) as $args ) {
				$this->add_field( $args );
			}
		} else {
			$this->add_field([
				'id'               => 'page_primary_hotel',
				'type'             => 'select',
				'name'             => esc_html__( 'Primary Hotel', 'awebooking' ),
				'options_cb'       => WP_Data::cb( 'pages', [ 'post_type' => 'hotel_location' ] ),
				'tooltip'          => esc_html__( 'Select the primary hotel.', 'awebooking' ),
				'sanitization_cb'  => 'absint',
				'classes'          => 'with-selectize',
				'show_option_none' => false,
			]);
		}

		// Enable multiple_hotels.
		if ( abrs_multiple_hotels() && ! abrs_list_hotels()->isEmpty() ) {
			$this->add_field([
				'id'       => '__hotel_listing',
				'type'     => 'title',
				'name'     => esc_html__( 'Hotel Listing', 'awebooking' ),
			]);

			$this->add_field([
				'id'              => 'list_hotels_order',
				'type'            => 'include',
				'name'            => esc_html__( 'Hotels', 'awebooking' ),
				'include'         => trailingslashit( dirname( __DIR__ ) ) . 'views/settings/html-hotel-listing.php',
				'save_fields'     => false,
			]);
		}
	}

	/**
	 * {@inheritdoc}
	 */
	public function save( Request $request ) {
		parent::save( $request );

		foreach ( (array) $request->get( 'list_hotels_order', [] ) as $order => $id ) {
			$hotel = ( new Hotel( $id ) );

			if ( $hotel->exists() ) {
				$saved = $hotel->fill( compact( 'order' ) )->save();
			}
		}
	}
}
