<?php
namespace Skeleton\Walker;

// We'll need the nav menu stuff here.
if ( ! class_exists( 'Walker_Nav_Menu_Edit' ) ) {
	require_once ABSPATH . 'wp-admin/includes/nav-menu.php';
}

/**
 * Walker for the administration nav menu editing.
 *
 * @uses Walker_Nav_Menu_Edit
 */
class Nav_Menu_Edit_Walker extends \Walker_Nav_Menu_Edit {
	/**
	 * Start the element output.
	 *
	 * @param string $output Passed by reference. Used to append additional content.
	 * @param object $item   Menu item data object.
	 * @param int    $depth  Depth of menu item. Used for padding.
	 * @param array  $args   An array of arguments. @see wp_nav_menu().
	 * @param int    $id     Current item ID.
	 */
	public function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {
		$item_output = '';
		parent::start_el( $item_output, $item, $depth, $args, $id );

		$output .= preg_replace(
			// NOTE: Check this regex from time to time!
			'/(?=<(p|fieldset)[^>]+class="[^"]*field-move)/',
			$this->custom_fields( $item, $depth, $args, $id ),
			$item_output
		);
	}

	/**
	 * Get custom fields.
	 *
	 * @param  object $item  Menu item data object.
	 * @param  int    $depth Depth of menu item. Used for padding.
	 * @param  array  $args  Menu item args.
	 * @param  int    $id    Nav menu ID.
	 * @return string
	 */
	protected function custom_fields( $item, $depth, $args = array(), $id = 0 ) {
		ob_start();

		/**
		 * Get menu item custom fields from plugins/themes.
		 *
		 * @param object $item  Menu item data object.
		 * @param int    $depth  Depth of menu item. Used for padding.
		 * @param array  $args  Menu item args.
		 * @param int    $id    Nav menu ID.
		 * @return string
		 */
		do_action( 'skeleton/nav_menu_edit_walker/custom_fields', $id, $item, $depth, $args );

		return ob_get_clean();
	}
}
