<?php
namespace Skeleton\Webfonts;

use Skeleton\Skeleton;

class Webfonts {
	/**
	 * Google Fonts Service.
	 */
	const GOOGLE_FONTS_SERVICE = 'https://www.googleapis.com/webfonts/v1/webfonts';
	const GOOGLE_FONTS_TRANSIENT = 'skeleton/google_fonts';

	/**
	 * The Skeleton instance.
	 *
	 * @var Skeleton
	 */
	protected $skeleton;

	/**
	 * Constructor Webfonts.
	 *
	 * @param Skeleton $skeleton Skeleton skeleton instance.
	 */
	public function __construct( Skeleton $skeleton ) {
		$this->skeleton = $skeleton;
	}

	/**
	 * Get all Google Fonts.
	 *
	 * @see https://developers.google.com/fonts/docs/developer_api
	 *
	 * @return array
	 */
	public function get_google_fonts() {
		// If found cached fonts in transient, just return it.
		if ( $cached_fonts = get_transient( static::GOOGLE_FONTS_TRANSIENT ) ) {
			return apply_filters( 'skeleton/webfonts/google_fonts', $cached_fonts );
		}

		$fonts = array();
		$google_fonts = $this->request_google_fonts();

		// If we can't get Google Fonts by user key, use from fallback.
		if ( empty( $google_fonts ) ) {
			$google_fonts = json_decode( wp_remote_fopen( $this->skeleton['webfonts-fallback'] ), true );
		}

		// Leave a empty array if can't get fonts by every way.
		if ( empty( $google_fonts['items'] ) ) {
			return $fonts;
		}

		// Build our fonts list.
		foreach ( $google_fonts['items'] as $item ) {
			$urls = array();

			// Get font properties from json array.
			foreach ( $item['variants'] as $variant ) {
				$name = str_replace( ' ', '+', $item['family'] );
				$urls[ $variant ] = "https://fonts.googleapis.com/css?family={$name}:{$variant}";
			}

			// Add this font to the fonts array.
			$uid = sanitize_key( $item['family'] );
			$fonts[ $uid ] = array(
				'name'         => $item['family'],
				'display_name' => $item['family'],
				'category'     => $item['category'],
				'font_type'    => 'google',
				'font_weights' => $item['variants'],
				'subsets'      => $item['subsets'],
				'files'        => $item['files'],
				'urls'         => $urls,
			);
		}

		// Set transient for google fonts.
		set_transient( static::GOOGLE_FONTS_TRANSIENT, $fonts, 14 * DAY_IN_SECONDS );

		return apply_filters( 'skeleton/webfonts/google_fonts', $fonts );
	}

	/**
	 * Get all websafe fonts.
	 *
	 * @return array $fonts -
	 */
	public function get_websafe_fonts() {
		/**
		 * Declare websafe fonts.
		 *
		 * @see http://www.w3schools.com/cssref/css_websafe_fonts.asp
		 * @var array
		 */
		$websafe_fonts = array(
			// Serif Fonts.
			'Georgia'             => 'Georgia, serif',
			'Palatino Linotype'   => '"Palatino Linotype", "Book Antiqua", Palatino, serif',
			'Times New Roman'     => '"Times New Roman", Times, serif',
			// Sans-Serif Fonts.
			'Arial'               => 'Arial, Helvetica, sans-serif',
			'Arial Black'         => '"Arial Black", Gadget, sans-serif',
			'Comic Sans MS'       => '"Comic Sans MS", cursive, sans-serif',
			'Impact'              => 'Impact, Charcoal, sans-serif',
			'Lucida Sans Unicode' => '"Lucida Sans Unicode", "Lucida Grande", sans-serif',
			'Tahoma'              => 'Tahoma, Geneva, sans-serif',
			'Trebuchet MS'        => '"Trebuchet MS", Helvetica, sans-serif',
			'Verdana'             => 'Verdana, Geneva, sans-serif',
			// Monospace Fonts.
			'Courier New'         => '"Courier New", Courier, monospace',
			'Lucida Console'      => '"Lucida Console", Monaco, monospace',
		);

		// Default websafe font weights.
		$weights = array( '400', '400italic', '700', '700italic' );

		// Build font list to return.
		$fonts = array();
		foreach ( $websafe_fonts as $font => $font_name ) {
			$uid = sanitize_key( $font );
			$fonts[ $uid ] = array(
				'name'         => $font_name,
				'display_name' => $font,
				'category'     => '',
				'font_type'    => 'websafe',
				'font_weights' => $weights,
				'subsets'      => array(),
				'files'        => array(),
				'urls'         => array(),
			);
		}

		return apply_filters( 'skeleton/webfonts/websafe_fonts', $fonts );
	}

	/**
	 * Get all fonts.
	 *
	 * @param string $format Which format will be return "flat" or "assoc".
	 * @return array
	 */
	public function get_all_fonts( $format = 'flat' ) {
		$websafe_fonts = (array) $this->get_websafe_fonts();
		$google_fonts  = (array) $this->get_google_fonts();

		if ( 'flat' === $format ) {
			return array_merge( $websafe_fonts, $google_fonts );
		}

		return array(
			array(
				'label' => esc_html__( 'Web Safe Fonts', 'skeleton' ),
				'fonts' => $websafe_fonts,
			),
			array(
				'label' => esc_html__( 'Google Fonts', 'skeleton' ),
				'fonts' => $google_fonts,
			),
		);
	}

	/**
	 * Get individual fonts.
	 *
	 * @param  string $id Font ID.
	 * @return array|false
	 */
	public function get_font( $id ) {
		// Return websafe font from array if set.
		$websafe_fonts = $this->get_websafe_fonts();
		if ( isset( $websafe_fonts[ $id ] ) ) {
			return $websafe_fonts[ $id ];
		}

		// Return google font from array if set.
		$google_fonts = $this->get_google_fonts();
		if ( isset( $google_fonts[ $id ] ) ) {
			return $google_fonts[ $id ];
		}

		return false;
	}

	/**
	 * Delete Google Fonts transients.
	 */
	public function delete_transients() {
		delete_transient( static::GOOGLE_FONTS_TRANSIENT );
	}

	/**
	 * Get list of fonts from Google Fonts service.
	 *
	 * @see https://developers.google.com/fonts/docs/developer_api
	 *
	 * @param  string $api_key The Google Fonts API key.
	 * @return array|false
	 */
	public function request_google_fonts( $api_key = null ) {
		$api_key = $api_key ? $api_key : $this->skeleton->get_option( 'google_fonts_api_keys' );
		$response = wp_remote_get( static::GOOGLE_FONTS_SERVICE . '?sort=alpha' . ( $api_key ? "&key={$api_key}" : '' ), array( 'sslverify' => false ) );

		if ( 200 !== wp_remote_retrieve_response_code( $response ) ) {
			return false;
		}

		return json_decode( wp_remote_retrieve_body( $response ), true );
	}
}
