<?php
namespace AweBooking\Admin;

use Skeleton\Menu_Page;
use AweBooking\AweBooking;
use Skeleton\Container\Service_Hooks;

class Admin_Hooks extends Service_Hooks {
	/**
	 * Determine run init action only in admin.
	 *
	 * @var boolean
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
		new Permalink_Settings;
		new Admin_Setup_Wizard;

		new Admin_Settings( $awebooking['config'], $awebooking['admin_menu'] );

		new List_Tables\Booking_List_Table;
		new List_Tables\Room_Type_List_Table;
		new List_Tables\Service_List_Table;

		new Meta_Boxes\Room_Type_Meta_Boxes;
		new Meta_Boxes\Booking_Meta_Boxes;

		add_action( 'admin_enqueue_scripts', array( $this, 'admin_register_scripts' ), 20 );

		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
		add_filter( 'menu_order', array( $this, 'menu_order' ) );
		add_filter( 'custom_menu_order', '__return_true' );

		add_action( 'admin_head', array( $this, 'menu_highlight' ) );
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
		global $parent_file, $submenu_file, $post_type;

		switch ( $post_type ) {
			case 'awebooking':
			case 'awebooking_rate':
				$parent_file = 'awebooking';
			break;
		}
	}

	/**
	 * Enqueue admin scripts.
	 */
	public function admin_register_scripts() {
		$awebooking_url = awebooking()->plugin_url();

		/**
		 * Should we load minified files?
		 */
		$suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG == true ) ? '' : '.min';
		$suffix = '';

		/**
	 	* If we are debugging the site,
	 	* use a unique version every page load so as to ensure no cache issues.
		 */
		$version = AweBooking::VERSION;

		// Register vendor styles and scripts.
		wp_register_style( 'daterangepicker', $awebooking_url . '/assets/css/daterangepicker' . $suffix . '.css', array(), '2.1.25' );
		wp_register_style( 'awebooking-admin', $awebooking_url . '/assets/css/admin' . $suffix . '.css', array(), $version );

		wp_register_script( 'vuejs', $awebooking_url . '/assets/js/vuejs/vue' . $suffix . '.js', array(), '2.3.0' );
		wp_register_script( 'moment', $awebooking_url . '/assets/js/moment/moment' . $suffix . '.js', array(), '2.18.1' );
		wp_register_script( 'daterangepicker', $awebooking_url . '/assets/js/daterangepicker/daterangepicker' . $suffix . '.js', array( 'jquery', 'moment' ), '2.1.25', true );

		// Register awebooking main styles and scripts.
		wp_register_script( 'awebooking-admin', $awebooking_url . '/assets/js/admin/awebooking' . $suffix . '.js', array( 'vuejs', 'wp-util', 'wp-backbone', 'jquery-effects-highlight' ), $version, true );
		wp_register_script( 'awebooking-yearly-calendar', $awebooking_url . '/assets/js/abkng-calendar/yearly-calendar.js', array( 'wp-backbone', 'daterangepicker' ), $version, true );
		wp_register_script( 'awebooking-pricing-calendar', $awebooking_url . '/assets/js/abkng-calendar/pricing-calendar.js', array( 'wp-backbone', 'daterangepicker' ), $version, true );
		wp_register_script( 'awebooking-room-type-meta-boxes', $awebooking_url . '/assets/js/admin/room-type-meta-boxes' . $suffix . '.js', array( 'awebooking-admin' ), $version, true );
		wp_register_script( 'awebooking-create-booking', $awebooking_url . '/assets/js/admin/create-booking' . $suffix . '.js', array( 'awebooking-admin' ), $version, true );

		// Send AweBooking object.
		wp_localize_script( 'awebooking-admin', 'ABKNG', array(
			'ajax_url' => admin_url( 'admin-ajax.php' ),
			'strings'  => array(
				'warning' => esc_html__( 'Are you sure you want to do this?', 'awebooking' ),
				'ask_reduce_the_rooms' => esc_html__( 'Are you sure you want to do this?', 'awebooking' ),
			),
		) );

		// Enqueue JS.
		$current_screen = get_current_screen();

		wp_enqueue_style( 'awebooking-admin' );
		wp_enqueue_script( 'awebooking-admin' );

		do_action( 'awebooking/register_admin_scripts', $current_screen );
	}
}
