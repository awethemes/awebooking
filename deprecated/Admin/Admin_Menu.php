<?php
namespace AweBooking\Deprecated\Admin;

use AweBooking\Support\Collection;

class Admin_Menu {
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

		$this->init();
	}

	/**
	 * Init the hooks.
	 *
	 * @return void
	 */
	public function init() {
		add_action( 'admin_menu', [ $this, 'register_custom_submenu' ], 40 );
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
	 * Register custom submenu.
	 *
	 * @access private
	 */
	public function register_custom_submenu() {
		$this->submenus->each( function( $submenu, $submenu_id ) {
			$callback  = ( is_string( $submenu['function'] ) && class_exists( $submenu['function'] ) )
				? $this->create_page_callback( $submenu['function'] )
				: $submenu['function'];

			$submenu_hook = add_submenu_page( 'awebooking', $submenu['page_title'], $submenu['menu_title'], $submenu['capability'], $submenu_id, $callback );

			if ( $submenu['noheader'] && $submenu_hook ) {
				$this->no_admin_header( $submenu_hook );
			}
		});
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
}
