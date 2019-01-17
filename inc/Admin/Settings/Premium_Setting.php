<?php

namespace AweBooking\Admin\Settings;

use AweBooking\Premium;
use WPLibs\Http\Request;

class Premium_Setting extends Abstract_Setting {
	/**
	 * {@inheritdoc}
	 */
	protected function setup() {
		$this->form_id  = 'premium';
		$this->label    = esc_html__( 'Premium', 'awebooking' );
		$this->priority = 1000;
	}

	/**
	 * {@inheritdoc}
	 */
	public function setup_fields() {
		$this->add_field([
			'id'   => '__premium_title',
			'type' => 'title',
			'name' => esc_html__( 'Premium', 'awebooking' ),
			'desc' => abrs_esc_text( __( 'Join our <a href="https://awethemes.com/join">membership</a> to extend your <a href="https://awethemes.com/plugins/awebooking#premiumaddons">AweBooking</a>.', 'awebooking' ) ),
		]);

		$this->add_field([
			'id'         => 'api_code',
			'type'       => 'text',
			'name'       => esc_html__( 'API Code', 'awebooking' ),
			'desc'       => esc_html__( 'Please enter your API code to get addons updates.', 'awebooking' ),
			'default'    => get_option( 'awebooking_premium_api_code', '' ),
		]);
	}

	/**
	 * {@inheritdoc}
	 */
	public function save( Request $request ) {
		$api_code = $request->get( 'api_code' );

		// Prevent update if nothing change.
		if ( hash_equals( $api_code, Premium::get_api_code() ) ) {
			return null;
		}

		$response = Premium::verify_api_code( $api_code );

		Premium::update_api_code( true === $response ? $api_code : null );

		if ( is_wp_error( $response ) ) {
			abrs_admin_notices( $response->get_error_message(), 'error' );
			return false; // Disable success notice.
		}
	}
}
