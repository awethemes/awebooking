<?php
namespace AweBooking\Admin;

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
		$awebooking_url = awebooking()->plugin_url();

		/**
	 	* If we are debugging the site,
	 	* use a unique version every page load so as to ensure no cache issues.
		 */
		$version = AweBooking::VERSION;

		// Register vendor styles and scripts.
		wp_register_style( 'select2', $awebooking_url . '/assets/css/select2.css', [], '4.0.3' );
		wp_register_style( 'awebooking-admin', $awebooking_url . '/assets/css/admin.css', [ 'wp-jquery-ui-dialog', 'select2' ], $version );

		wp_register_script( 'moment', $awebooking_url . '/assets/js/moment/moment.js', [], '2.18.1' );
		wp_register_script( 'select2', $awebooking_url . '/assets/js/select2/select2.full.js', [ 'jquery' ], '4.0.3' );

		wp_register_script( 'awebooking-yearly-calendar', $awebooking_url . '/assets/js/abkng-calendar/yearly-calendar.js', [ 'wp-backbone' ], $version, true );
		wp_register_script( 'awebooking-pricing-calendar', $awebooking_url . '/assets/js/abkng-calendar/pricing-calendar.js', [ 'wp-backbone' ], $version, true );

		// Register awebooking main styles and scripts.
		$deps = [ 'awebooking-manifest', 'awebooking-vendor', 'moment', 'select2', 'wp-util', 'jquery-effects-highlight', 'jquery-ui-dialog', 'jquery-ui-datepicker' ];
		wp_register_script( 'awebooking-manifest', $awebooking_url . '/assets/js/admin/manifest.js', [], $version, true );
		wp_register_script( 'awebooking-vendor', $awebooking_url . '/assets/js/admin/vendor.js', [], $version, true );
		wp_register_script( 'awebooking-admin', $awebooking_url . '/assets/js/admin/awebooking.js', $deps, $version, true );

		wp_register_script( 'awebooking-edit-booking', awebooking()->plugin_url() . '/assets/js/admin/edit-booking.js', array( 'awebooking-admin' ), $version, true );
		wp_register_script( 'awebooking-edit-service', awebooking()->plugin_url() . '/assets/js/admin/edit-service.js', array( 'awebooking-admin' ), $version, true );
		wp_register_script( 'awebooking-edit-room-type', awebooking()->plugin_url() . '/assets/js/admin/edit-room-type.js', array( 'awebooking-admin' ), $version, true );

		wp_register_script( 'awebooking-manager-pricing', $awebooking_url . '/assets/js/admin/manager-pricing.js', [ 'awebooking-admin', 'awebooking-pricing-calendar' ], $version, true );
		wp_register_script( 'awebooking-manager-availability', $awebooking_url . '/assets/js/admin/manager-availability.js', [ 'awebooking-admin', 'awebooking-yearly-calendar' ], $version, true );

		// Send AweBooking object.
		wp_localize_script( 'awebooking-admin', '_awebookingSettings', array(
			'ajax_url' => admin_url( 'admin-ajax.php' ),
			'strings'  => array(
				'warning' => esc_html__( 'Are you sure you want to do this?', 'awebooking' ),
				'ask_reduce_the_rooms' => esc_html__( 'Are you sure you want to do this?', 'awebooking' ),
			),
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

		if ( AweBooking::ROOM_TYPE === $screen->id ) {
			wp_enqueue_script( 'awebooking-edit-room-type' );
		}

		if ( AweBooking::BOOKING === $screen->id ) {
			wp_enqueue_script( 'awebooking-edit-booking' );
		}

		if ( 'edit-hotel_extra_service' === $screen->id ) {
			// wp_enqueue_script( 'awebooking-edit-service' );
		}

		if ( $awebooking_screen_id . '_page_manager-pricing' === $screen->id ) {
			wp_enqueue_script( 'awebooking-manager-pricing' );
		}

		if ( $awebooking_screen_id . '_page_manager-awebooking' === $screen->id ) {
			wp_enqueue_script( 'awebooking-manager-availability' );
		}
	}
}
