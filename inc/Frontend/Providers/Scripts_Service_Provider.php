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
		$suffix  = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
		$version = $this->plugin->version();

		// Register core JS.
		abrs_register_vendor_js();

		// Core JS & CSS.
		wp_register_style( 'awebooking', abrs_asset_url( 'css/awebooking.css' ), [ 'flatpickr', 'tippy', 'react-calendar' ], $version );
		wp_register_style( 'awebooking-iconfont', abrs_asset_url( 'fonts/awebooking-webfont.css' ), [], $version );
		wp_register_style( 'awebooking-colour', abrs_asset_url( 'css/awebooking-colour.css' ), [ 'awebooking-iconfont', 'awebooking' ], $version );
		wp_register_script( 'awebooking', abrs_asset_url( 'js/awebooking' . $suffix . '.js' ), [ 'jquery', 'flatpickr', 'tippy', 'a11y-dialog' ], $version, true );
		wp_register_script( 'awebooking-checkout', abrs_asset_url( 'js/checkout' . $suffix . '.js' ), [ 'awebooking', 'knockout' ], $version, true );

		$deps = [ 'awebooking', 'knockout', 'moment' ];
		if ( 'on' === abrs_get_option( 'use_experiment_style', 'off' ) ) {
			$deps[] = 'react-calendar';
		}

		wp_register_script( 'awebooking-search-form', abrs_asset_url( 'js/search-form' . $suffix . '.js' ), $deps, $version, true );
	}

	/**
	 * Enqueue frontend scripts.
	 *
	 * @access private
	 */
	public function enqueue_scripts() {
		wp_enqueue_style( 'awebooking' );
		if ( $styles = apply_filters( 'abrs_custom_css', trim( abrs_get_option( 'custom_css' ) ) ) ) {
			wp_add_inline_style( 'awebooking', $styles );
		}

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
			'route'      => abrs_route( '/' ),
			'datepicker' => [
				'minNights'    => abrs_get_option( 'display_datepicker_minnights', 1 ),
				'maxNights'    => abrs_get_option( 'display_datepicker_maxnights', 0 ),
				'minDate'      => abrs_get_option( 'display_datepicker_mindate', 0 ),
				'maxDate'      => abrs_get_option( 'display_datepicker_maxdate', 0 ),
				'disableDates' => abrs_get_option( 'display_datepicker_disabledates', '' ),
				'disableDays'  => abrs_get_option( 'display_datepicker_disabledays', [] ),
				'showMonths'   => abrs_get_option( 'display_datepicker_showmonths', 1 ),
			],
		]);

		switch ( true ) {
			case abrs_is_checkout_page():
				wp_enqueue_script( 'awebooking-checkout' );
				break;
		}
	}
}
