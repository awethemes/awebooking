<?php
namespace AweBooking\Admin\Providers;

use AweBooking\Constants;
use AweBooking\Support\Service_Provider;
use Illuminate\Support\Arr;

class Menu_Service_Provider extends Service_Provider {
	/**
	 * Init the hooks.
	 *
	 * @return void
	 */
	public function init() {
		add_action( 'admin_menu', [ $this, 'register_admin_menu' ], 9 );
		add_action( 'admin_menu', [ $this, 'register_manager_submenu' ], 20 );
		add_action( 'admin_menu', [ $this, 'regsiter_settings_submenu' ], 50 );

		add_filter( 'custom_menu_order', '__return_true' );
		add_filter( 'menu_order', [ $this, 'menu_order' ] );
		add_action( 'admin_head', [ $this, 'cleanup_submenu' ] );

		add_action( 'admin_head', [ $this, 'correct_admin_menus' ], 5 );
		add_filter( 'admin_title', [ $this, 'correct_admin_title' ], 5, 2 );
	}

	/**
	 * Register the "awebooking" menu.
	 *
	 * @access private
	 */
	public function register_admin_menu() {
		global $menu;

		// @codingStandardsIgnoreLine
		$menu[] = [ '', 'read', 'separator-awebooking', '', 'wp-menu-separator awebooking' ];

		add_menu_page( esc_html__( 'AweBooking', 'awebooking' ), esc_html__( 'AweBooking', 'awebooking' ), 'manage_awebooking', 'awebooking', null, 'dashicons-calendar', 53 );

		add_submenu_page( 'awebooking', esc_html__( 'About', 'awebooking' ), esc_html__( 'About', 'awebooking' ), 'manage_awebooking', 'admin.php?awebooking=/about' );
	}

	/**
	 * Register the "management" submenu.
	 *
	 * @access private
	 */
	public function register_manager_submenu() {
		// add_submenu_page( 'awebooking', esc_html__( 'New Booking', 'awebooking' ), esc_html_x( 'New Booking', 'dashboard menu', 'awebooking' ), 'manage_awebooking', 'admin.php?awebooking=/reservation' );

		add_submenu_page( 'awebooking', esc_html__( 'Calendar', 'awebooking' ), esc_html_x( 'Calendar', 'dashboard menu', 'awebooking' ), 'manage_awebooking', 'admin.php?awebooking=/calendar' );

		add_submenu_page( 'awebooking', esc_html__( 'Pricing', 'awebooking' ), esc_html_x( 'Pricing', 'dashboard menu', 'awebooking' ), 'manage_awebooking', 'admin.php?awebooking=/rates' );
	}

	/**
	 * Register the "setting" submenu.
	 *
	 * @access private
	 */
	public function regsiter_settings_submenu() {
		add_submenu_page( 'awebooking', esc_html__( 'Settings', 'awebooking' ), esc_html__( 'Settings', 'awebooking' ), 'manage_awebooking', 'admin.php?awebooking=/settings' );
	}

	/**
	 * Reorder the WP menu items in admin.
	 *
	 * @param  array $menu_order The original menu_order.
	 * @return array
	 *
	 * @access private
	 */
	public function menu_order( $menu_order ) {
		$separator_index = array_search( 'separator-awebooking', $menu_order );
		$room_type_index = array_search( 'edit.php?post_type=room_type', $menu_order );

		$new_menu = [];
		foreach ( $menu_order as $index => $item ) {
			if ( 'awebooking' == $item ) {
				$new_menu[] = 'separator-awebooking';
				$new_menu[] = $item;
				$new_menu[] = 'edit.php?post_type=room_type';

				unset( $menu_order[ $room_type_index ] );
				unset( $menu_order[ $separator_index ] );
			} elseif ( ! in_array( $item, array( 'separator-awebooking' ) ) ) {
				$new_menu[] = $item;
			}
		}

		return $new_menu;
	}

	/**
	 * Clean-up the submenu.
	 *
	 * @access private
	 */
	public function cleanup_submenu() {
		global $submenu;

		// Remove 'AweBooking' sub menu item.
		if ( isset( $submenu['awebooking'] ) ) {
			unset( $submenu['awebooking'][0] );
		}

		remove_submenu_page( 'edit.php?post_type=room_type', 'post-new.php?post_type=room_type' );
	}

	/**
	 * Highlights the correct admin-menus.
	 *
	 * @access private
	 */
	public function correct_admin_menus() {
		global $parent_file, $submenu_file, $submenu;

		$current_screen = get_current_screen();
		if ( 'awebooking_route' === $current_screen->base ) {
			// @codingStandardsIgnoreStart
			$parent_file  = 'awebooking';
			$submenu_file = 'admin.php?awebooking=' . $this->plugin['request']->route_path();

			if ( 0 === strpos( $current_screen->id, 'awebooking/booking' ) ) {
				$submenu_file = 'edit.php?post_type=awebooking';
			}
			// @codingStandardsIgnoreEnd
		}
	}

	/**
	 * Correct the title tag content for an admin page.
	 *
	 * @param  string $admin_title The page title, with extra context added.
	 * @param  string $title       The original page title.
	 * @return string
	 *
	 * @access private
	 */
	public function correct_admin_title( $admin_title, $title ) {
		global $submenu;

		$current_screen = get_current_screen();
		if ( 'awebooking_route' !== $current_screen->base || ! isset( $submenu['awebooking'] ) ) {
			return $admin_title;
		}

		$found_submenu = Arr::first( $submenu['awebooking'], function( $submenu_item ) {
			return 'admin.php?awebooking=' . awebooking( 'request' )->route_path() === $submenu_item[2];
		});

		return $found_submenu ? $found_submenu[3] . $admin_title : $admin_title;
	}
}
