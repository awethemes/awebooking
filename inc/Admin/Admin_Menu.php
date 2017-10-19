<?php
namespace AweBooking\Admin;

use Skeleton\Admin_Menu as Base_Admin_Menu;
use AweBooking\Admin\Pages\Admin_Welcome;
use AweBooking\Admin\Pages\Pricing_Management;
use AweBooking\Admin\Pages\Availability_Management;

class Admin_Menu extends Base_Admin_Menu {
	/**
	 * Constructor admin menu.
	 */
	public function __construct() {
		parent::__construct( 'awebooking', array(
			'page_title' => esc_html__( 'AweBooking', 'awebooking' ),
			'menu_title' => esc_html__( 'AweBooking', 'awebooking' ),
			'icon_url'   => 'dashicons-calendar',
			'position'   => 53,
			'function' => function() {
				(new Admin_Welcome)->output();
			},
		));

		$this->add_submenu( 'manager-awebooking', array(
			'page_title'  => esc_html__( 'Manager Availability', 'awebooking' ),
			'menu_title'  => esc_html__( 'Manager Availability', 'awebooking' ),
			'noheader'    => true,
			'function' => function() {
				(new Availability_Management)->output();
			},
		));

		$this->add_submenu( 'manager-pricing', array(
			'page_title'  => esc_html__( 'Manager Pricing', 'awebooking' ),
			'menu_title'  => esc_html__( 'Manager Pricing', 'awebooking' ),
			'noheader'    => true,
			'function' => function() {
				(new Pricing_Management)->output();
			},
		));

		$this->add_submenu( 'edit.php?post_type=awebooking', array(
			'page_title'  => esc_html__( 'Booking', 'awebooking' ),
			'menu_title'  => esc_html__( 'Booking', 'awebooking' ),
		));
	}

	/**
	 * Trigger admin_init hook.
	 */
	public function init() {
		parent::init();

		add_filter( 'custom_menu_order', '__return_true' );
		add_action( 'admin_menu', array( $this, '_add_menu_separator' ) );
		add_filter( 'menu_order', array( $this, '_menu_order' ) );
		add_action( 'admin_head', array( $this, '_menu_highlight' ) );
	}

	public function _add_menu_separator() {
		global $menu;
		$menu[] = array( '', 'read', 'separator-awebooking', '', 'wp-menu-separator awebooking' );
	}

	public function _menu_order( $menu_order ) {
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
	public function _menu_highlight() {
		global $parent_file, $submenu_file;
		$current_screen = get_current_screen();

		if ( ! $current_screen ) {
			return;
		}

		switch ( $current_screen->id ) {
			case 'awebooking':
				$parent_file  = 'awebooking';
				$submenu_file = 'edit.php?post_type=awebooking';
			break;
		}
	}
}
