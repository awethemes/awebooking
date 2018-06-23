<?php
namespace AweBooking\Frontend\Providers;

use AweBooking\Support\Service_Provider;

class Scripts_Service_Provider extends Service_Provider {
	/**
	 * Init (boot) the service provider.
	 *
	 * @return void
	 */
	public function init() {
		add_action( 'wp_enqueue_scripts', [ $this, 'register_scripts' ], 5 );
		add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_scripts' ], 9 );
		add_action( 'wp_enqueue_scripts', 'abrs_localize_flatpickr', 1000 );
	}

	/**
	 * Register frontend scripts.
	 *
	 * @access private
	 */
	public function register_scripts() {
		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
		$version = $this->plugin->version();

		// Vendor.
		wp_register_style( 'flatpickr', ABRS_ASSET_URL . 'vendor/flatpickr/flatpickr.css', [], '4.5.0' );
		wp_register_script( 'flatpickr', ABRS_ASSET_URL . 'vendor/flatpickr/flatpickr' . $suffix . '.js', [], '4.5.0', true );

		wp_register_style( 'sweetalert2', ABRS_ASSET_URL . 'vendor/sweetalert2/sweetalert2' . $suffix . '.css', [], '7.21' );
		wp_register_script( 'sweetalert2', ABRS_ASSET_URL . 'vendor/sweetalert2/sweetalert2' . $suffix . '.js', [], '7.21', true );

		wp_register_style( 'tippy', ABRS_ASSET_URL . 'vendor/tippy.js/tippy.css', [], '2.5.2' );
		wp_register_script( 'tippy', ABRS_ASSET_URL . 'vendor/tippy.js/tippy' . $suffix . '.js', [], '2.5.2', true );

		// Core JS & CSS.
		wp_register_style( 'awebooking-iconfont', ABRS_ASSET_URL . 'fonts/awebooking-webfont.css', [], $version );
		wp_register_style( 'awebooking', ABRS_ASSET_URL . 'css/awebooking.css', [ 'flatpickr', 'tippy' ], $version );
		wp_register_style( 'awebooking-colour', ABRS_ASSET_URL . 'css/awebooking-colour.css', [ 'awebooking-iconfont', 'awebooking' ], $version );

		wp_register_script( 'awebooking', ABRS_ASSET_URL . 'js/awebooking.js', [ 'jquery', 'flatpickr', 'tippy' ], $version, true );
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

		if ( apply_filters( 'abrs_enqueue_default_style', true ) ) {
			wp_enqueue_style( 'awebooking-colour' );
		}

		wp_enqueue_script( 'awebooking' );
		wp_localize_script( 'awebooking', '_awebooking', [
			'ajax_url'    => admin_url( 'admin-ajax.php' ),
			'route'       => $this->plugin['url']->route( '/' ),
			'i18n'        => [
				'date_format' => abrs_get_date_format(),
				'time_format' => abrs_get_time_format(),
			],
			'datepicker' => [
				'min_nights'   => abrs_get_option( 'display_datepicker_minnights' ),
				'max_nights'   => abrs_get_option( 'display_datepicker_maxnights' ),
				'min_date'     => abrs_get_option( 'display_datepicker_mindate' ),
				'max_date'     => abrs_get_option( 'display_datepicker_maxdate' ),
				'disable'      => abrs_get_option( 'display_datepicker_disabledates' ),
				'disable_days' => abrs_get_option( 'display_datepicker_disabledays' ),
				'show_months'  => abrs_get_option( 'display_datepicker_showmonths', 2 ),
			],
		]);

		switch ( true ) {
			case abrs_is_search_page():
				wp_enqueue_script( 'awebooking-search' );
				break;
			case abrs_is_checkout_page():
				wp_enqueue_script( 'awebooking-checkout' );
				break;
		}
	}
}
