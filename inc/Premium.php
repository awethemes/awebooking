<?php

namespace AweBooking;

use WP_Error;

class Premium {
	/* Constants */
	const VERIFY_URL = 'https://awethemes.com/validate';
	const ADDONS_URL = 'https://update.awethemes.com/addons.json';
	const THEMES_URL = 'https://update.awethemes.com/themes.json';

	/**
	 * The premium addons (support for the update).
	 *
	 * @var array
	 */
	public static $addons = [
		'awebooking-payment'            => 'awebooking-payment/awebooking-payment.php',
		'awebooking-form-builder'       => 'awebooking-form-builder/awebooking-form-builder.php',
		'awebooking-icalendar'          => 'awebooking-icalendar/awebooking-icalendar.php',
		'awebooking-recaptcha'          => 'awebooking-recaptcha/awebooking-recaptcha.php',
		'awebooking-rates'              => 'awebooking-rates/awebooking-rates.php',
		'awebooking-rules'              => 'awebooking-rules/awebooking-rules.php',
		'awebooking-simple-reservation' => 'awebooking-simple-reservation/awebooking-simple-reservation.php',
		'awebooking-woocommerce'        => 'awebooking-woocommerce/awebooking-woocommerce.php',
	];

	/**
	 * Gets the API code.
	 *
	 * @return string
	 */
	public static function get_api_code() {
		return get_option( 'awebooking_premium_api_code', '' );
	}

	/**
	 * Perform update the api code.
	 *
	 * @param string $code The api code.
	 */
	public static function update_api_code( $code ) {
		if ( null === $code ) {
			delete_option( 'awebooking_premium_api_code' );
		} else {
			update_option( 'awebooking_premium_api_code', $code );
		}
	}

	/**
	 * Verify the API code.
	 *
	 * @param string $code The API code.
	 *
	 * @return bool|\WP_Error
	 */
	public static function verify_api_code( $code ) {
		global $wp_version;

		$data = [
			'code'               => 'Awetheme_validate_api_xxx',
			'api'                => $code,
			'domain'             => parse_url( home_url( '' ), PHP_URL_HOST ),
			'wordpress_version'  => $wp_version,
			'awebooking_version' => awebooking()->version(),
		];

		$response = wp_remote_post( static::VERIFY_URL, [
			'sslverify' => false,
			'timeout' => 30,
			'body'    => $data,
		] );

		if ( 200 !== wp_remote_retrieve_response_code( $response ) ) {
			return new WP_Error( 'server_error', esc_html__( 'Sorry, we can\'t verify your API code right now. Please try again later.', 'awebooking' ) );
		}

		$response = @json_decode( wp_remote_retrieve_body( $response ), true );

		// Successfully.
		if ( isset( $response['status'] ) && 1 === (int) $response['status'] ) {
			return true;
		}

		return new WP_Error( 'invalid',
			! empty( $response['error'] ) ? $response['error'] : esc_html__( 'Sorry, your API code is invalid. Please enter again.', 'awebooking' )
		);
	}

	/**
	 * Gets the premium plugins.
	 *
	 * @return array
	 */
	public static function get_premium_plugins() {
		$addons = get_transient( 'awebooking_premium_addons' );

		if ( ! $addons ) {
			$addons = static::remote_get( static::ADDONS_URL );

			set_transient( 'awebooking_premium_addons', $addons, 7 * DAY_IN_SECONDS );
		}

		return apply_filters( 'abrs_premium_addons', (array) $addons );
	}

	/**
	 * Gets the premium themes.
	 *
	 * @return array
	 */
	public static function get_premium_themes() {
		$themes = get_transient( 'awebooking_premium_themes' );

		if ( ! $themes ) {
			$themes = static::remote_get( static::THEMES_URL );

			set_transient( 'awebooking_premium_themes', $themes, 7 * DAY_IN_SECONDS );
		}

		return apply_filters( 'abrs_premium_themes', (array) $themes );
	}

	/**
	 * Request get remote json data.
	 *
	 * @param string $link The link.
	 * @return array
	 */
	protected static function remote_get( $link ) {
		$response = wp_remote_get( $link, [ 'sslverify' => false ] );

		if ( 200 !== wp_remote_retrieve_response_code( $response ) ) {
			return [];
		}

		return @json_decode( wp_remote_retrieve_body( $response ), true );
	}
}
