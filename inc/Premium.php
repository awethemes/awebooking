<?php
namespace AweBooking;

class Premium {
	/* Constants */
	const ADDONS_URL = 'http://update.awethemes.local/addons.json';
	const THEMES_URL = 'http://update.awethemes.local/themes.json';

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
