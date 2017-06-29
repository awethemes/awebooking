<?php
namespace Skeleton\Walker;

use Skeleton\Container\Service_Hooks;

class Walker_Hooks extends Service_Hooks {
	/**
	 * Init service provider.
	 *
	 * This method will be run after container booted.
	 *
	 * @param Skeleton $skeleton Skeleton instance.
	 */
	public function init( $skeleton ) {
		add_filter( 'wp_edit_nav_menu_walker', array( $this, 'nav_menu_edit_walker' ) );
	}

	/**
	 * Filter the walker being used for the menu edit screen.
	 *
	 * @return string
	 */
	public function nav_menu_edit_walker() {
		return 'Skeleton\Walker\Nav_Menu_Edit_Walker';
	}
}
