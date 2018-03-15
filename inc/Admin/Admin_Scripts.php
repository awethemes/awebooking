<?php
namespace AweBooking\Admin;

use AweBooking\Constants;
use AweBooking\AweBooking;

class Admin_Scripts {
	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'admin_enqueue_scripts', array( $this, 'register_scripts' ) );
	}

	/**
	 * Register admin scripts.
	 */
	public function register_scripts() {
		$version = AweBooking::VERSION;
		$awebooking_url = awebooking()->plugin_url();

		// Register vendor styles and scripts.
		wp_register_style( 'select2', $awebooking_url . 'assets/css/select2.css', [], '4.0.3' );
		wp_register_style( 'flatpickr', $awebooking_url . 'assets/css/flatpickr.css', [], '4.3.2' );
		wp_register_style( 'sweetalert2', $awebooking_url . 'assets/css/sweetalert2.css', [], '7.9.0' );
		wp_register_style( 'awebooking-iconfont', $awebooking_url . 'assets/fonts/awebooking-webfont.css', [], $version );
		wp_register_style( 'awebooking-admin', $awebooking_url . 'assets/css/admin.css', [ 'awebooking-iconfont', 'wp-jquery-ui-dialog', 'select2', 'flatpickr', 'sweetalert2' ], $version );

		wp_register_script( 'moment', $awebooking_url . 'assets/js/moment/moment.js', [], '2.18.1' );
		wp_register_script( 'select2', $awebooking_url . 'assets/js/select2/select2.full.js', [ 'jquery' ], '4.0.3' );
		wp_register_script( 'sweetalert2', $awebooking_url . 'assets/js/sweetalert2/sweetalert2.min.js', [], '7.9.0' );
		wp_register_script( 'waypoints', $awebooking_url . 'assets/js/waypoints/waypoints.min.js', [], '4.0.1' );

		// Register awebooking main styles and scripts.
		$deps = [ 'awebooking-manifest', 'awebooking-vendor', 'moment', 'select2', 'wp-util', 'jquery-effects-highlight', 'jquery-ui-dialog', 'jquery-ui-datepicker', 'sweetalert2' ];
		wp_register_script( 'awebooking-manifest', $awebooking_url . 'assets/js/admin/manifest.js', [], $version, true );
		wp_register_script( 'awebooking-vendor', $awebooking_url . 'assets/js/admin/vendor.js', [], $version, true );
		wp_register_script( 'awebooking-admin', $awebooking_url . 'assets/js/admin/awebooking.js', $deps, $version, true );

		wp_register_script( 'awebooking-scheduler', $awebooking_url . 'assets/js/admin/schedule-calendar.js', [ 'backbone', 'waypoints', 'awebooking-admin' ], $version, true );
		wp_register_script( 'awebooking-manager-pricing', $awebooking_url . 'assets/js/admin/manager-pricing.js', [ 'awebooking-scheduler' ], $version, true );

		wp_register_script( 'awebooking-edit-booking', $awebooking_url . 'assets/js/admin/edit-booking.js', array( 'awebooking-admin' ), $version, true );
		wp_register_script( 'awebooking-edit-service', $awebooking_url . 'assets/js/admin/edit-service.js', array( 'awebooking-admin' ), $version, true );
		wp_register_script( 'awebooking-edit-room-type', $awebooking_url . 'assets/js/admin/edit-room-type.js', array( 'awebooking-admin' ), $version, true );

		// Send AweBooking object.
		wp_localize_script( 'awebooking-admin', '_awebooking', array(
			'ajax_url'        => admin_url( 'admin-ajax.php' ),
			'date_format'     => Constants::DATE_FORMAT,
			'date_alt_format' => awebooking( 'setting' )->get_date_format(),
			'strings'         => [
				'ok'              => esc_html__( 'OK', 'awebooking' ),
				'delete'          => esc_html__( 'Delete', 'awebooking' ),
				'cancel'          => esc_html__( 'Cancel', 'awebooking' ),
				'warning'         => esc_html__( 'Are you sure you want to do this?', 'awebooking' ),
				'confirm_title'   => esc_html__( 'Are you sure?', 'awebooking' ),
				'confirm_message' => esc_html__( 'You won\'t be able to revert this!', 'awebooking' ),
				'ask_reduce_the_rooms' => esc_html__( 'Are you sure you want to do this?', 'awebooking' ),
			],
		) );

		do_action( 'awebooking/register_admin_scripts' );

		$this->enqueue_scripts();
	}

	/**
	 * Register admin scripts.
	 *
	 * @return void
	 */
	public function enqueue_scripts() {
		$screen = get_current_screen();

		/**
		 * Hack for checking submenu-page ID correcly.
		 *
		 * WP core use menu_title to construct subpage load-hooks, so when "AweBooking"
		 * (which we use in Menu) is translated to something else, $screen->id will change.
		 *
		 * @see https://www.skyverge.com/blog/screen-id-checks-wordpress-submenu-pages/
		 *
		 * @var string
		 */
		$awebooking_screen_id = sanitize_title( esc_html__( 'AweBooking', 'awebooking' ) );

		wp_enqueue_style( 'cmb2-styles' );
		wp_enqueue_style( 'awebooking-admin' );
		wp_enqueue_script( 'awebooking-admin' );

		if ( Constants::ROOM_TYPE === $screen->id ) {
			wp_enqueue_script( 'awebooking-edit-room-type' );
		}

		if ( Constants::BOOKING === $screen->id ) {
			wp_enqueue_script( 'awebooking-edit-booking' );
		}
	}
}
