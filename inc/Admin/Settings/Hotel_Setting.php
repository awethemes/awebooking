<?php
namespace AweBooking\Admin\Settings;

use Awethemes\Http\Request;
use AweBooking\Admin\Forms\Hotel_Information_Form;
use AweBooking\Model\Hotel;

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
	 * {@inheritdoc}
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
			'desc'            => esc_html__( 'The hotel name.', 'awebooking' ),
			'tooltip'         => true,
		]);

		foreach ( ( new Hotel_Information_Form )->prop( 'fields' ) as $args ) {
			$this->add_field( $args );
		}

		// Enable multiple_hotels.
		if ( abrs_get_option( 'enable_location', false ) ) {
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
			$saved = ( new Hotel( $id ) )->fill( compact( 'order' ) )->save();
		}
	}
}
