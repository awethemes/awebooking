<?php
namespace AweBooking\Admin;

use AweBooking\AweBooking;
use Skeleton\Container\Service_Hooks;

class Admin_Scripts_Hooks extends Service_Hooks {

	/**
	 * Init service provider.
	 *
	 * This method will be run after container booted.
	 *
	 * @param Skeleton $awebooking AweBooking Container instance.
	 */
	public function init( $awebooking ) {
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
		wp_register_style( 'awebooking-admin', $awebooking_url . '/assets/css/admin.css', array( 'wp-jquery-ui-dialog' ), $version );

		wp_register_script( 'vuejs', $awebooking_url . '/assets/js/vuejs/vue.js', array(), '2.3.0' );
		wp_register_script( 'moment', $awebooking_url . '/assets/js/moment/moment.js', array(), '2.18.1' );

		// Register awebooking main styles and scripts.
		$deps = [ 'vuejs', 'moment', 'wp-util', 'jquery-effects-highlight', 'jquery-ui-dialog', 'jquery-ui-datepicker' ];
		wp_register_script( 'awebooking-admin', $awebooking_url . '/assets/js/admin/awebooking.js', $deps, $version, true );

		wp_register_script( 'awebooking-yearly-calendar', $awebooking_url . '/assets/js/abkng-calendar/yearly-calendar.js', array( 'wp-backbone' ), $version, true );
		wp_register_script( 'awebooking-pricing-calendar', $awebooking_url . '/assets/js/abkng-calendar/pricing-calendar.js', array( 'wp-backbone' ), $version, true );

		wp_register_script( 'awebooking-admin-manager-pricing', $awebooking_url . '/assets/js/admin/manager-pricing.js', [ 'awebooking-admin', 'awebooking-pricing-calendar' ], $version, true );
		wp_register_script( 'awebooking-admin-manager-availability', $awebooking_url . '/assets/js/admin/manager-availability.js', [ 'awebooking-admin', 'awebooking-yearly-calendar' ], $version, true );

		wp_register_script( 'awebooking-room-type-meta-boxes', $awebooking_url . '/assets/js/admin/room-type-meta-boxes.js', array( 'awebooking-admin' ), $version, true );
		wp_register_script( 'awebooking-create-booking', $awebooking_url . '/assets/js/admin/create-booking.js', array( 'awebooking-admin' ), $version, true );

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
		$current_screen = get_current_screen();

		wp_enqueue_style( 'cmb2-styles' );
		wp_enqueue_style( 'awebooking-admin' );
		wp_enqueue_script( 'awebooking-admin' );

		if ( 'awebooking_page_manager-pricing' === $current_screen->id ) {
			wp_enqueue_script( 'awebooking-admin-manager-pricing' );
		}

		if ( 'awebooking_page_manager-awebooking' === $current_screen->id ) {
			wp_enqueue_script( 'awebooking-admin-manager-availability' );
		}
	}
}
