<?php

namespace AweBooking\Admin\Providers;

use CMB2_hookup;
use CMB2_Type_Colorpicker;
use AweBooking\Support\Service_Provider;

class Scripts_Service_Provider extends Service_Provider {
	/**
	 * Init the hooks.
	 *
	 * @access private
	 */
	public function init() {
		add_action( 'admin_enqueue_scripts', [ $this, 'register_scripts' ], 5 );
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_scripts' ], 10 );
		add_action( 'admin_enqueue_scripts', 'abrs_localize_flatpickr', 1000 );
	}

	/**
	 * Register admin scripts.
	 *
	 * @access private
	 */
	public function register_scripts() {
		$min     = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
		$version = $this->plugin->version();

		// Vendor JS.
		abrs_register_vendor_js();

		// Core styles & scripts.
		wp_register_style( 'awebooking-iconfont', abrs_asset_url( 'fonts/awebooking-webfont.css' ), [], $version );
		wp_register_style( 'awebooking-admin', abrs_asset_url( 'css/admin' . $min . '.css' ), [ 'awebooking-iconfont', 'flatpickr', 'tippy', 'selectize', 'sweetalert2' ], $version );
		wp_register_style( 'awebooking-scheduler', abrs_asset_url( 'css/schedule-calendar' . $min . '.css' ), [ 'awebooking-admin' ], $version );

		wp_register_script( 'awebooking-admin', abrs_asset_url( 'js/admin/admin' . $min . '.js' ), [ 'jquery', 'knockout', 'wp-util', 'flatpickr', 'tippy', 'selectize', 'sweetalert2' ], $version, true );
		wp_register_script( 'awebooking-scheduler', abrs_asset_url( 'js/admin/schedule-calendar' . $min . '.js' ), [ 'backbone', 'moment', 'jquery-waypoints', 'awebooking-admin' ], $version, true );
		wp_register_script( 'awebooking-settings', abrs_asset_url( 'js/admin/settings' . $min . '.js' ), [ 'awebooking-admin', 'sortable' ], $version, true );
		wp_register_script( 'awebooking-edit-booking', abrs_asset_url( 'js/admin/edit-booking' . $min . '.js' ), [ 'awebooking-admin' ], $version, true );
		wp_register_script( 'awebooking-edit-room-type', abrs_asset_url( 'js/admin/edit-room-type' . $min . '.js' ), [ 'awebooking-admin', 'sortable', 'jquery-effects-highlight' ], $version, true );
		wp_register_script( 'awebooking-page-rates', abrs_asset_url( 'js/admin/page-pricing' . $min . '.js' ), [ 'awebooking-admin', 'awebooking-scheduler', 'jquery-ui-dialog' ], $version, true );
		wp_register_script( 'awebooking-page-calendar', abrs_asset_url( 'js/admin/page-calendar' . $min . '.js' ), [ 'awebooking-admin', 'awebooking-scheduler', 'jquery-ui-dialog' ], $version, true );
	}

	/**
	 * Enqueue admin scripts.
	 *
	 * @access private
	 */
	public function enqueue_scripts() {
		if ( ! $screen = get_current_screen() ) {
			return;
		}

		// Enqueue global awebooking admin JS & CSS.
		if ( 'widgets' === $screen->id
			|| 'awebooking_route' === $screen->base
			|| in_array( $screen->id, abrs_admin_screens() ) ) {
			CMB2_hookup::enqueue_cmb_js();
			CMB2_hookup::enqueue_cmb_css();

			// Preapre setup CMB2, @see CMB2::add_field().
			add_filter( 'wp_prepare_attachment_for_js', [ 'CMB2_Type_File_Base', 'prepare_image_sizes_for_js' ], 10, 3 );
			CMB2_Type_Colorpicker::dequeue_rgba_colorpicker_script();
			cmb2_ajax();

			wp_enqueue_style( 'awebooking-admin' );
			wp_enqueue_script( 'awebooking-admin' );

			wp_localize_script( 'awebooking-admin', 'awebooking', [
				'debug'       => defined( 'WP_DEBUG' ) && WP_DEBUG,
				'ajax_url'    => admin_url( 'admin-ajax.php' ),
				'admin_route' => $this->plugin['url']->admin_route(),
				'i18n'        => [
					'date_format' => abrs_get_date_format(),
					'time_format' => abrs_get_time_format(),
					'ok'          => esc_html__( 'OK', 'awebooking' ),
					'cancel'      => esc_html__( 'Cancel', 'awebooking' ),
					'warning'     => esc_html__( 'Are you sure you want to do this?', 'awebooking' ),
					'error'       => esc_html__( 'Something went wrong. Please try again.', 'awebooking' ),

					'numberDecimals'    => abrs_get_option( 'price_number_decimals' ),
					'decimalSeparator'  => abrs_get_option( 'price_decimal_separator' ),
					'thousandSeparator' => abrs_get_option( 'price_thousand_separator' ),
					'currencySymbol'    => abrs_currency_symbol(),
					// Format to accounting.js, @see http://openexchangerates.github.io/accounting.js/.
					'priceFormat'       => str_replace( [ '%1$s', '%2$s' ], [ '%s', '%v' ], abrs_get_price_format() ),
				],
			]);
		}

		// Enqueue the scripts depends by the route.
		switch ( true ) {
			case ( 'room_type' === $screen->id ):
				wp_enqueue_script( 'awebooking-edit-room-type' );
				break;

			case ( 'awebooking' === $screen->id ):
				wp_enqueue_script( 'awebooking-edit-booking' );
				wp_localize_script( 'awebooking-edit-booking', '_awebookingEditBooking', [
					'add_note_nonce'    => wp_create_nonce( 'awebooking_add_note' ),
					'delete_note_nonce' => wp_create_nonce( 'awebooking_delete_note' ),
					'i18n'              => [
						'delete_note_warning' => esc_html__( 'Are you sure you wish to delete this note?', 'awebooking' ),
						'empty_note_warning'  => esc_html__( 'Please enter some content into note', 'awebooking' ),
					],
				]);
				break;

			case abrs_admin_route_is( '/rates' ):
				wp_enqueue_style( 'wp-jquery-ui-dialog' );
				wp_enqueue_style( 'awebooking-scheduler' );
				wp_enqueue_script( 'awebooking-page-rates' );
				break;

			case abrs_admin_route_is( '/calendar' ):
				wp_enqueue_style( 'wp-jquery-ui-dialog' );
				wp_enqueue_style( 'awebooking-scheduler' );
				wp_enqueue_script( 'awebooking-page-calendar' );
				break;

			case abrs_admin_route_is( '/settings' ):
				wp_enqueue_script( 'awebooking-settings' );
				wp_localize_script( 'awebooking-settings', '_awebookingSettings', [
					'i18n' => [
						'nav_warning' => esc_html__( 'The changes you made will be lost if you navigate away from this page.', 'awebooking' ),
					],
				]);
				break;
		}
	}
}
