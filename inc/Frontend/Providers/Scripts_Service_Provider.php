<?php
namespace AweBooking\Frontend\Providers;

use AweBooking\Constants;
use AweBooking\Support\Service_Provider;

class Scripts_Service_Provider extends Service_Provider {
	/**
	 * Init (boot) the service provider.
	 *
	 * @return void
	 */
	public function init() {
		add_action( 'wp_enqueue_scripts', [ $this, 'register_scripts' ], 9 );
		add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_scripts' ], 10 );
		add_action( 'wp_enqueue_scripts', 'abrs_localize_flatpickr', 1000 );
	}

	/**
	 * Register frontend scripts.
	 *
	 * @access private
	 */
	public function register_scripts() {
		$min = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
		$version = $this->plugin->version();

		// Vendor.
		wp_register_style( 'flatpickr', ABRS_ASSET_URL . 'vendor/flatpickr/flatpickr.css', [], '4.4.4' );
		wp_register_script( 'flatpickr', ABRS_ASSET_URL . 'vendor/flatpickr/flatpickr' . $min . '.js', [ 'flatpickr-range-plugin' ], '4.4.4', true );
		wp_register_script( 'flatpickr-range-plugin', ABRS_ASSET_URL . 'vendor/flatpickr/rangePlugin.js', [], '4.4.4', true );

		wp_register_style( 'sweetalert2', ABRS_ASSET_URL . 'vendor/sweetalert2/sweetalert2' . $min . '.css', [], '7.18.0' );
		wp_register_script( 'sweetalert2', ABRS_ASSET_URL . 'vendor/sweetalert2/sweetalert2' . $min . '.js', [], '7.18.0', true );

		wp_register_script( 'magnific-popup', ABRS_ASSET_URL . 'js/magnific-popup/jquery.magnific-popup.min.js', [ 'jquery' ], '1.1.0', true );

		// Core JS & CSS.
		wp_register_style( 'awebooking-iconfont', ABRS_ASSET_URL . 'fonts/awebooking-webfont.css', [], $version );
		wp_register_style( 'awebooking', ABRS_ASSET_URL . 'css/awebooking.css', [ 'flatpickr' ], $version );
		wp_register_style( 'awebooking-colour', ABRS_ASSET_URL . 'css/awebooking-colour.css', [ 'awebooking-iconfont', 'awebooking' ], $version );

		wp_register_script( 'awebooking', ABRS_ASSET_URL . 'js/awebooking.js', [ 'jquery', 'flatpickr' ], $version, true );
		wp_register_script( 'awebooking-search', ABRS_ASSET_URL . 'js/main-search.js', [ 'awebooking' ], $version, true );
		wp_register_script( 'awebooking-checkout', ABRS_ASSET_URL . 'js/checkout.js', [ 'awebooking' ], $version, true );
	}

	/**
	 * Enqueue frontend scripts.
	 *
	 * @access private
	 */
	public function enqueue_scripts() {
		wp_enqueue_style( 'awebooking' );

		if ( apply_filters( 'awebooking/enqueue_default_style', true ) ) {
			wp_enqueue_style( 'awebooking-colour' );
		}

		wp_enqueue_script( 'awebooking' );
		wp_localize_script( 'awebooking', '_awebooking', [
			'ajax_url'    => admin_url( 'admin-ajax.php' ),
			'route'       => $this->plugin['url']->route( '/' ),
			'i18n'        => [
				'date_format' => abrs_date_format(),
				'time_format' => abrs_time_format(),
			],
		]);

		switch ( true ) {
			case is_awebooking_search():
				wp_enqueue_script( 'awebooking-search' );
				break;
		}
	}
}
