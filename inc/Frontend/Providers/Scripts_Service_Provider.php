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

		wp_register_script( 'moment', ABRS_ASSET_URL . 'vendor/moment/moment' . $suffix . '.js', [], '2.22.1', false );
		wp_register_script( 'knockout', ABRS_ASSET_URL . 'vendor/knockout/knockout-latest' . ( $suffix ? '' : '.debug' ) . '.js', [ 'jquery' ], '3.4.2', false );
		wp_register_script( 'a11y-dialog', ABRS_ASSET_URL . 'vendor/a11y-dialog/a11y-dialog' . $suffix . '.js', [], '5.1.1', true );
		wp_register_style( 'flatpickr', ABRS_ASSET_URL . 'vendor/flatpickr/flatpickr.css', [], '4.5.0' );
		wp_register_script( 'flatpickr', ABRS_ASSET_URL . 'vendor/flatpickr/flatpickr' . $suffix . '.js', [], '4.5.0', true );
		wp_register_style( 'sweetalert2', ABRS_ASSET_URL . 'vendor/sweetalert2/sweetalert2' . $suffix . '.css', [], '7.21' );
		wp_register_script( 'sweetalert2', ABRS_ASSET_URL . 'vendor/sweetalert2/sweetalert2' . $suffix . '.js', [], '7.21', true );
		wp_register_style( 'tippy', ABRS_ASSET_URL . 'vendor/tippy.js/tippy.css', [], '2.5.2' );
		wp_register_script( 'tippy', ABRS_ASSET_URL . 'vendor/tippy.js/tippy' . $suffix . '.js', [], '2.5.2', true );

		// Core JS & CSS.
		wp_register_style( 'awebooking', ABRS_ASSET_URL . 'css/awebooking.css', [ 'flatpickr', 'tippy' ], $version );
		wp_register_style( 'awebooking-iconfont', ABRS_ASSET_URL . 'fonts/awebooking-webfont.css', [], $version );
		wp_register_style( 'awebooking-colour', ABRS_ASSET_URL . 'css/awebooking-colour.css', [ 'awebooking-iconfont', 'awebooking' ], $version );
		wp_register_script( 'awebooking', ABRS_ASSET_URL . 'js/awebooking' . $suffix . '.js', [ 'jquery', 'flatpickr', 'tippy', 'a11y-dialog' ], $version, true );
		wp_register_script( 'awebooking-search-form', ABRS_ASSET_URL . 'js/search-form' . $suffix . '.js', [ 'awebooking', 'knockout' ], $version, true );
		wp_register_script( 'awebooking-checkout', ABRS_ASSET_URL . 'js/checkout' . $suffix . '.js', [ 'awebooking', 'knockout' ], $version, true );
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
		wp_enqueue_script( 'awebooking-search-form' );

		wp_localize_script( 'awebooking', '_awebooking_i18n', [
			'dateFormat'        => abrs_get_date_format(),
			'timeFormat'        => abrs_get_time_format(),
			'numberDecimals'    => abrs_get_option( 'price_number_decimals' ),
			'decimalSeparator'  => abrs_get_option( 'price_decimal_separator' ),
			'thousandSeparator' => abrs_get_option( 'price_thousand_separator' ),
			'currencySymbol'    => abrs_currency_symbol(),
			// Format to accounting.js, @see http://openexchangerates.github.io/accounting.js/.
			'priceFormat'       => str_replace( [ '%1$s', '%2$s' ], [ '%s', '%v' ], abrs_get_price_format() ),
		]);

		wp_localize_script( 'awebooking', '_awebooking', [
			'ajax_url'   => admin_url( 'admin-ajax.php' ),
			'route'      => $this->plugin['url']->route( '/' ),
			'datepicker' => [
				'minNights'   => abrs_get_option( 'display_datepicker_minnights' ),
				'maxMights'   => abrs_get_option( 'display_datepicker_maxnights' ),
				'minDate'     => abrs_get_option( 'display_datepicker_mindate' ),
				'maxDate'     => abrs_get_option( 'display_datepicker_maxdate' ),
				'disable'     => abrs_get_option( 'display_datepicker_disabledates' ),
				'disableDays' => abrs_get_option( 'display_datepicker_disabledays' ),
				'showMonths'  => abrs_get_option( 'display_datepicker_showmonths', 2 ),
			],
		]);

		switch ( true ) {
			case abrs_is_checkout_page():
				wp_enqueue_script( 'awebooking-checkout' );
				break;
		}
	}
}
