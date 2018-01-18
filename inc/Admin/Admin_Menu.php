<?php
namespace AweBooking\Admin;

use AweBooking\Constants;
use AweBooking\Support\Collection;
use AweBooking\Admin\Pages\About_Page;
use AweBooking\Admin\Pages\Settings_Page;
use AweBooking\Admin\Pages\Pricing_Management;
use AweBooking\Admin\Pages\Availability_Management;

class Admin_Menu {
	/* Constants */
	const PARENT_SLUG = 'awebooking';

	/**
	 * An array submenu will be add to top-level menu.
	 *
	 * @var \AweBooking\Support\Collection
	 */
	protected $submenus;

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->submenus = new Collection;
	}

	/**
	 * Init the hooks.
	 *
	 * @return void
	 */
	public function init() {
		add_action( 'admin_menu', [ $this, 'register_admin_menu' ], 9 );
		add_action( 'admin_menu', [ $this, 'register_manager_submenu' ], 20 );
		add_action( 'admin_menu', [ $this, 'register_custom_submenu' ], 40 );
		add_action( 'admin_menu', [ $this, 'regsiter_setting_submenu' ], 60 );
		add_action( 'admin_menu', [ $this, 'register_about_submenu' ], 80 );

		add_filter( 'custom_menu_order', '__return_true' );
		add_filter( 'menu_order', [ $this, 'menu_order' ] );
		add_action( 'admin_head', [ $this, 'cleanup_submenu' ] );
	}

	/**
	 * Add a submenu to a top-level menu.
	 *
	 * @param  string $submenu_id   Submenu ID.
	 * @param  array  $submenu_args Submenu array arguments.
	 * @return void
	 */
	public function add_submenu( $submenu_id, array $submenu_args = [] ) {
		$submenu_args = wp_parse_args( $submenu_args, [
			'page_title' => '',
			'menu_title' => '',
			'function'   => null,
			'capability' => 'manage_awebooking',
			'priority'   => 10,
			'noheader'   => false,
		]);

		$this->submenus->put(
			$submenu_id, $submenu_args
		);
	}

	/**
	 * Get the submenus.
	 *
	 * @return \AweBooking\Support\Collection
	 */
	public function get_submenus() {
		return $this->submenus;
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

		add_menu_page( esc_html__( 'AweBooking', 'awebooking' ), esc_html__( 'AweBooking', 'awebooking' ), 'manage_awebooking', static::PARENT_SLUG, null, 'dashicons-calendar', 53 );
	}

	/**
	 * Register custom submenu.
	 *
	 * @access private
	 */
	public function register_custom_submenu() {
		$this->submenus->each( function( $submenu, $submenu_id ) {
			$callback  = ( is_string( $submenu['function'] ) && class_exists( $submenu['function'] ) )
				? $this->create_page_callback( $submenu['function'] )
				: $submenu['function'];

			$submenu_hook = add_submenu_page( static::PARENT_SLUG, $submenu['page_title'], $submenu['menu_title'], $submenu['capability'], $submenu_id, $callback );

			if ( $submenu['noheader'] && $submenu_hook ) {
				$this->no_admin_header( $submenu_hook );
			}
		});
	}

	/**
	 * Register the "management" submenu.
	 *
	 * @access private
	 */
	public function register_manager_submenu() {
		$availability_page_hook = add_submenu_page( static::PARENT_SLUG, esc_html__( 'Manager Availability', 'awebooking' ), esc_html__( 'Availability', 'awebooking' ), 'manage_awebooking', 'awebooking-availability', $this->create_page_callback( Availability_Management::class ) );
		$this->no_admin_header( $availability_page_hook );

		$pricing_page_hook = add_submenu_page( static::PARENT_SLUG, esc_html__( 'Manager Pricing', 'awebooking' ), esc_html__( 'Pricing', 'awebooking' ), 'manage_awebooking', 'awebooking-pricing', $this->create_page_callback( Pricing_Management::class ) );
		$this->no_admin_header( $pricing_page_hook );
	}

	/**
	 * Register the "setting" submenu.
	 *
	 * @access private
	 */
	public function regsiter_setting_submenu() {
		add_submenu_page( static::PARENT_SLUG, esc_html__( 'AweBooking Settings', 'awebooking' ), esc_html__( 'Settings', 'awebooking' ), 'manage_awebooking', 'awebooking-settings', $this->create_page_callback( Settings_Page::class ) );
	}

	/**
	 * Register the "about" menu item.
	 *
	 * @access private
	 */
	public function register_about_submenu() {
		add_submenu_page( static::PARENT_SLUG, esc_html__( 'AweBooking About', 'awebooking' ), esc_html__( 'About', 'awebooking' ), 'manage_awebooking', 'awebooking-about', $this->create_page_callback( About_Page::class ) );
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
		$hotel_index     = array_search( Constants::ADMIN_PAGE_HOTEL, $menu_order );

		$new_menu = [];
		foreach ( $menu_order as $index => $item ) {
			if ( 'awebooking' == $item ) {
				$new_menu[] = 'separator-awebooking';
				$new_menu[] = $item;
				$new_menu[] = Constants::ADMIN_PAGE_HOTEL;

				unset( $menu_order[ $hotel_index ] );
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
	 * No admin header.
	 *
	 * ```
	 * require_once ABSPATH . 'wp-admin/admin-header.php';
	 * ```
	 *
	 * @param  string $page_hook Page hook name.
	 * @return void
	 */
	protected function no_admin_header( $page_hook ) {
		add_action( 'load-' . $page_hook, function() {
			$_GET['noheader'] = true;
		});
	}

	/**
	 * Create the page callback for "add_submenu_page" function.
	 *
	 * @param  string $page_class The page class to resolve.
	 * @return Closure
	 */
	protected function create_page_callback( $page_class ) {
		return function() use ( $page_class ) {
			awebooking()->call( $page_class, [], 'output' );
		};
	}
}
