<?php
namespace AweBooking\Admin;

use Skeleton\Menu_Page;
use AweBooking\Installer;
use AweBooking\AweBooking;
use AweBooking\Support\Service_Hooks;

class Admin_Hooks extends Service_Hooks {
	/**
	 * Determine run init action only in admin.
	 *
	 * @var bool
	 */
	public $in_admin = true;

	/**
	 * Registers services on the given container.
	 *
	 * This method should only be used to configure services and parameters.
	 *
	 * @param Container $container Container instance.
	 */
	public function register( $container ) {
		$container->singleton( 'admin_notices', function() {
			return new Admin_Notices;
		});

		$container->singleton( 'admin_menu', function() {
			return new Admin_Menu;
		});
	}

	/**
	 * Init service provider.
	 *
	 * This method will be run after container booted.
	 *
	 * @param Skeleton $awebooking AweBooking Container instance.
	 */
	public function init( $awebooking ) {
		$awebooking['admin_menu']->init();

		new Admin_Ajax;
		new Action_Handler;
		new Admin_Scripts;

		new Admin_Settings;
		new Pages\Permalink_Settings;
		new Pages\Admin_Email_Preview;
		new Pages\Admin_Setup_Wizard;

		new List_Tables\Booking_List_Table;
		new List_Tables\Room_Type_List_Table;
		new List_Tables\Service_List_Table;

		new Metaboxes\Booking_Metabox;
		new Metaboxes\Service_Metabox;
		new Metaboxes\Room_Type_Metabox;

		add_action( 'admin_init', array( $this, 'admin_init' ) );
		add_action( 'admin_notices', [ $awebooking['admin_notices'], 'display' ] );
		add_filter( 'display_post_states', array( $this, 'page_state' ), 10, 2 );
	}

	/**
	 * Handle redirects to setup/welcome page after install and updates.
	 *
	 * For setup wizard, transient must be present, the user must have access rights, and we must ignore the network/bulk plugin updaters.
	 */
	public function admin_init() {
		// Setup wizard redirect.
		if ( get_transient( '_awebooking_activation_redirect' ) ) {
			delete_transient( '_awebooking_activation_redirect' );

			// Prevent redirect on some case.
			if ( ( ! empty( $_GET['page'] ) && in_array( $_GET['page'], [ 'awebooking-setup' ] ) ) || is_network_admin() ) {
				return;
			}

			// If the user needs to install, send them to the setup wizard.
			wp_safe_redirect( admin_url( 'index.php?page=awebooking-setup' ) );
			exit;
		}

		// Run background update.
		$db_version = get_option( 'awebooking_version' );
		if ( ! $db_version || AweBooking::VERSION !== $db_version ) {
			Installer::update();
		}
	}

	/**
	 * Add state for check availability page. TODO: Move to admin page.
	 *
	 * @param  array $post_states post_states.
	 * @param  void  $post        post object.
	 *
	 * @return array
	 */
	public function page_state( $post_states, $post ) {
		if ( intval( awebooking_option( 'page_check_availability' ) ) === $post->ID ) {
			$post_states['page_check_availability'] = esc_html__( 'Check Availability Page', 'awebooking' );
		}

		if ( intval( awebooking_option( 'page_booking' ) ) === $post->ID ) {
			$post_states['page_booking'] = esc_html__( 'Booking Informations Page', 'awebooking' );
		}

		if ( intval( awebooking_option( 'page_checkout' ) ) === $post->ID ) {
			$post_states['page_checkout'] = esc_html__( 'Checkout Page', 'awebooking' );
		}

		return $post_states;
	}
}
