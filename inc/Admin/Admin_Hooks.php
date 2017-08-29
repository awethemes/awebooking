<?php
namespace AweBooking\Admin;

use Skeleton\Menu_Page;
use AweBooking\AweBooking;
use AweBooking\Support\Admin_Notices;
use Skeleton\Container\Service_Hooks;

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
		$container->trigger( new Admin_Scripts_Hooks );

		$container->bind( 'admin_notices', function() {
			return new Admin_Notices;
		});

		$container->bind( 'admin_welcome', function() {
			return new Admin_Welcome;
		});

		$container['admin_welcome']->add_tab( array(
			'id'       => 'welcome',
			'title'    => esc_html__( 'Welcome', 'awebooking' ),
			'nowrap'   => true,
			'callback' => function() {
				include_once __DIR__ . '/views/welcome-tabs/admin_welcome.php';
			},
		));

		$container['admin_menu'] = new Menu_Page( 'awebooking', array(
			'page_title' => esc_html__( 'AweBooking', 'awebooking' ),
			'menu_title' => esc_html__( 'AweBooking', 'awebooking' ),
			'icon_url'   => 'dashicons-calendar',
			'position'   => 52,
			'function' => function() {
				awebooking( 'admin_welcome' )->output();
			},
		));

		$container['admin_menu']->add_submenu( 'manager-awebooking', array(
			'page_title'  => esc_html__( 'Manager Availability', 'awebooking' ),
			'menu_title'  => esc_html__( 'Manager Availability', 'awebooking' ),
			'noheader'    => true,
			'function' => function() {
				(new Availability_Management)->output();
			},
		));

		$container['admin_menu']->add_submenu( 'manager-pricing', array(
			'page_title'  => esc_html__( 'Manager Pricing', 'awebooking' ),
			'menu_title'  => esc_html__( 'Manager Pricing', 'awebooking' ),
			'noheader'    => true,
			'function' => function() {
				(new Pricing_Management)->output();
			},
		));

		$container['admin_menu']->add_submenu( 'edit.php?post_type=awebooking', array(
			'page_title'  => esc_html__( 'Booking', 'awebooking' ),
			'menu_title'  => esc_html__( 'Booking', 'awebooking' ),
		));
	}

	/**
	 * Init service provider.
	 *
	 * This method will be run after container booted.
	 *
	 * @param Skeleton $awebooking AweBooking Container instance.
	 */
	public function init( $awebooking ) {
		$awebooking->make( 'admin_menu' )->init();

		new Admin_Ajax;
		new Action_Handler;
		new Permalink_Settings;
		new Admin_Setup_Wizard;
		new Admin_Email_Preview;

		new Admin_Settings( $awebooking['admin_menu'] );

		new List_Tables\Booking_List_Table;
		new List_Tables\Room_Type_List_Table;
		new List_Tables\Service_List_Table;

		new Metaboxes\Room_Type_Metabox;
		new Metaboxes\Booking_Metabox;
		new Metaboxes\Service_Metabox;

		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
		add_filter( 'menu_order', array( $this, 'menu_order' ) );
		add_filter( 'custom_menu_order', '__return_true' );

		add_action( 'admin_head', array( $this, 'menu_highlight' ) );

		add_filter( 'display_post_states', array( $this, 'page_state' ), 10, 2 );

		add_action( 'admin_init', array( $this, 'admin_redirects' ) );
		add_action( 'admin_notices', [ $awebooking['admin_notices'], 'display' ] );
	}

	/**
	 * Handle redirects to setup/welcome page after install and updates.
	 *
	 * For setup wizard, transient must be present, the user must have access rights, and we must ignore the network/bulk plugin updaters.
	 */
	public function admin_redirects() {
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
			$post_states['page_check_availability'] = __( 'Check Availability Page' );
		}

		if ( intval( awebooking_option( 'page_booking' ) ) === $post->ID ) {
			$post_states['page_booking'] = __( 'Booking Informations Page' );
		}

		if ( intval( awebooking_option( 'page_checkout' ) ) === $post->ID ) {
			$post_states['page_checkout'] = __( 'Checkout Page' );
		}

		return $post_states;
	}

	public function admin_menu() {
		global $menu;
		$menu[] = array( '', 'read', 'separator-awebooking', '', 'wp-menu-separator awebooking' );
	}

	public function menu_order( $menu_order ) {
		$awebooking_menu_order = array();

		$awebooking_separator = array_search( 'separator-awebooking', $menu_order );

		foreach ( $menu_order as $index => $item ) {
			if ( ( ( 'edit.php?post_type=room_type' ) == $item ) ) {
				$awebooking_menu_order[] = 'separator-awebooking';
				$awebooking_menu_order[] = $item;
				unset( $menu_order[ $awebooking_separator ] );
			} elseif ( ! in_array( $item, array( 'separator-awebooking' ) ) ) {
				$awebooking_menu_order[] = $item;
			}
		}

		return $awebooking_menu_order;
	}

	/**
	 * Highlights the correct top level admin menu item for post type add screens.
	 */
	public function menu_highlight() {
		global $parent_file, $submenu_file;

		$current_screen = get_current_screen();

		switch ( $current_screen->id ) {
			case 'awebooking':
			case 'admin_page_awebooking-add-item':
				$parent_file  = 'awebooking';
				$submenu_file = 'edit.php?post_type=awebooking';
			break;
		}
	}
}
