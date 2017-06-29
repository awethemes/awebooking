<?php

namespace Skeleton;

use Skeleton\Support\Priority_List;

/**
 * Easy build top-level/submenu menu page.
 *
 * @see https://developer.wordpress.org/reference/functions/add_menu_page
 * @see https://developer.wordpress.org/reference/functions/add_submenu_page
 */
class Menu_Page {
	/**
	 * Top-level menu page ID.
	 *
	 * @var string
	 */
	protected $topmenu;

	/**
	 * Top-level menu page args.
	 *
	 * @var array
	 */
	protected $topmenu_args = array();

	/**
	 * An array submenu will be add to top-level menu.
	 *
	 * @var array
	 */
	protected $submenus = array();

	/**
	 * Create a top-level menu.
	 *
	 * @param string $topmenu      Top-level menu page ID.
	 * @param array  $topmenu_args Top-level menu page args.
	 */
	public function __construct( $topmenu, array $topmenu_args = array() ) {
		$this->topmenu = $topmenu;

		$this->topmenu_args = wp_parse_args( $topmenu_args, array(
			'page_title' => '',
			'menu_title' => '',
			'capability' => 'manage_options',
			'function'   => '',
			'icon_url'   => '',
			'position'   => null,
			'noheader'   => false,
		));

		$this->topmenu_args['menu_slug'] = $this->topmenu;
	}

	/**
	 * Trigger admin_init hook.
	 */
	public function init() {
		add_action( 'admin_menu', array( $this, 'admin_menu' ), 5 );
	}

	/**
	 * Return topmenu slug.
	 *
	 * @return string
	 */
	public function get_topmenu() {
		return $this->topmenu;
	}

	/**
	 * Add a submenu to a top-level menu.
	 *
	 * @param string $submenu_id   Submenu ID.
	 * @param array  $submenu_args Submenu array arguments.
	 */
	public function add_submenu( $submenu_id, array $submenu_args = array() ) {
		$submenu_args = wp_parse_args( $submenu_args, array(
			'parent_slug' => $this->topmenu,
			'page_title'  => '',
			'menu_title'  => '',
			'capability'  => 'manage_options',
			'function'    => '',
			'noheader'    => false,
			'priority'    => isset( $submenu_args['priority'] ) ? (int) $submenu_args['priority'] : 10,
		));

		$submenu_args['menu_slug'] = $submenu_id;
		$this->submenus[ $submenu_id ] = $submenu_args;
	}

	/**
	 * Add registered topmenu/submenus to admin menus.
	 *
	 * @access private
	 */
	public function admin_menu() {
		$topmenu = $this->topmenu_args;

		if ( $this->topmenu ) {
			$topmenu_hook = add_menu_page( $topmenu['page_title'], $topmenu['menu_title'], $topmenu['capability'], $topmenu['menu_slug'], $topmenu['function'], $topmenu['icon_url'], $topmenu['position'] );

			if ( $topmenu_hook && $topmenu['noheader'] ) {
				add_action( 'load-' . $topmenu_hook, array( $this, 'noheader' ) );
			}
		}

		foreach ( $this->submenus as  $submenu ) {
			$submenu_hook = add_submenu_page( $submenu['parent_slug'], $submenu['page_title'], $submenu['menu_title'], $submenu['capability'], $submenu['menu_slug'], $submenu['function'] );

			if ( $submenu_hook && $submenu['noheader'] ) {
				add_action( 'load-' . $submenu_hook, array( $this, 'noheader' ) );
			}
		}
	}

	/**
	 * Page with no header.
	 *
	 * @access private
	 */
	public function noheader() {
		$_GET['noheader'] = true;
	}
}
