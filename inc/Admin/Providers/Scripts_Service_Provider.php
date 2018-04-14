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
		add_action( 'admin_enqueue_scripts', [ $this, 'register_scripts' ], 9 );
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_scripts' ], 11 );
		add_action( 'admin_enqueue_scripts', 'abrs_localize_flatpickr', 1000 );
	}

	/**
	 * Register admin scripts.
	 *
	 * @access private
	 */
	public function register_scripts() {
		$min = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
		$version = $this->plugin->version();

		wp_register_script( 'moment', ABRS_ASSET_URL . 'vendor/moment/moment' . $min . '.js', [], '2.0.21', false );
		wp_register_script( 'jquery.waypoints', ABRS_ASSET_URL . 'vendor/waypoints/jquery.waypoints' . $min . '.js', [ 'jquery' ], '4.0.1', true );

		wp_register_style( 'flatpickr', ABRS_ASSET_URL . 'vendor/flatpickr/confetti.css', [], '4.4.4' );
		wp_register_script( 'flatpickr', ABRS_ASSET_URL . 'vendor/flatpickr/flatpickr' . $min . '.js', [], '4.4.4', true );
		wp_register_script( 'flatpickr-range-plugin', ABRS_ASSET_URL . 'vendor/flatpickr/rangePlugin.js', [ 'flatpickr' ], '4.4.4', true );

		wp_register_style( 'tippy', ABRS_ASSET_URL . 'vendor/tippy.js/tippy.css', [], '2.4.1' );
		wp_register_script( 'tippy', ABRS_ASSET_URL . 'vendor/tippy.js/tippy' . $min . '.js', [], '2.4.1', true );

		wp_register_style( 'selectize', ABRS_ASSET_URL . 'vendor/selectize/selectize.css', [], '0.12.4' );
		wp_register_script( 'selectize', ABRS_ASSET_URL . 'vendor/selectize/selectize' . $min . '.js', [], '0.12.4', true );

		wp_register_style( 'sweetalert2', ABRS_ASSET_URL . 'vendor/sweetalert2/sweetalert2' . $min . '.css', [], '7.18.0' );
		wp_register_script( 'sweetalert2', ABRS_ASSET_URL . 'vendor/sweetalert2/sweetalert2' . $min . '.js', [], '7.18.0', true );

		// Core styles & scripts.
		wp_register_style( 'awebooking-iconfont', ABRS_ASSET_URL . 'fonts/awebooking-webfont.css', [], $version );
		wp_register_style( 'awebooking-admin', ABRS_ASSET_URL . 'css/admin.css', [ 'awebooking-iconfont', 'flatpickr', 'tippy', 'selectize', 'sweetalert2' ], $version );
		wp_register_style( 'awebooking-scheduler', ABRS_ASSET_URL . 'css/schedule-calendar.css', [ 'awebooking-admin' ], $version );

		wp_register_script( 'awebooking-admin', ABRS_ASSET_URL . 'js/admin/admin.js', [ 'jquery', 'wp-util', 'flatpickr', 'flatpickr-range-plugin', 'tippy', 'selectize', 'sweetalert2' ], $version, true );
		wp_register_script( 'awebooking-scheduler', ABRS_ASSET_URL . 'js/admin/schedule-calendar.js', [ 'backbone', 'moment', 'jquery.waypoints', 'awebooking-admin' ], $version, true );

		wp_register_script( 'awebooking-edit-booking', ABRS_ASSET_URL . 'js/admin/edit-booking.js', [ 'awebooking-admin' ], $version, true );
		wp_register_script( 'awebooking-edit-room-type', ABRS_ASSET_URL . 'js/admin/edit-room-type.js', [ 'awebooking-admin', 'jquery-ui-sortable' ], $version, true );
		wp_register_script( 'awebooking-settings', ABRS_ASSET_URL . 'js/admin/settings.js', [ 'awebooking-admin', 'jquery-ui-sortable' ], $version, true );
		wp_register_script( 'awebooking-page-rates', ABRS_ASSET_URL . 'js/admin/page-pricing.js', [ 'awebooking-admin', 'awebooking-scheduler', 'jquery-ui-dialog' ], $version, true );
		wp_register_script( 'awebooking-page-calendar', ABRS_ASSET_URL . 'js/admin/page-calendar.js', [ 'awebooking-admin', 'awebooking-scheduler', 'jquery-ui-dialog' ], $version, true );
	}

	/**
	 * Enqueue admin scripts.
	 *
	 * @param  string $hook_suffix The current admin page.
	 * @access private
	 */
	public function enqueue_scripts( $hook_suffix ) {
		$screen = get_current_screen();

		// Enqueue global awebooking admin JS & CSS.
		if ( 'awebooking_route' === $screen->base || in_array( $screen->id, abrs_admin_screens() ) ) {
			CMB2_hookup::enqueue_cmb_js();
			CMB2_hookup::enqueue_cmb_css();

			// Preapre setup CMB2, @see CMB2::add_field().
			add_filter( 'wp_prepare_attachment_for_js', [ 'CMB2_Type_File_Base', 'prepare_image_sizes_for_js' ], 10, 3 );
			CMB2_Type_Colorpicker::dequeue_rgba_colorpicker_script();
			cmb2_ajax();

			wp_enqueue_style( 'awebooking-admin' );
			wp_enqueue_script( 'awebooking-admin' );

			wp_localize_script( 'awebooking-admin', 'awebooking', [
				'debug'       => ( defined( 'WP_DEBUG' ) && WP_DEBUG ),
				'ajax_url'    => admin_url( 'admin-ajax.php' ),
				'admin_route' => $this->plugin['url']->admin_route(),
				'i18n'        => [
					'date_format' => abrs_date_format(),
					'time_format' => abrs_time_format(),
					'ok'          => esc_html__( 'OK', 'awebooking' ),
					'cancel'      => esc_html__( 'Cancel', 'awebooking' ),
					'warning'     => esc_html__( 'Are you sure you want to do this?', 'awebooking' ),
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
