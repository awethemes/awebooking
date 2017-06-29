<?php
namespace Skeleton\Iconfonts\Icons;

/**
 * Groups and icons must return below structure:
 *
 * Groups (if need):
 *
 * array(
 *      array(
 *          'id'   => 'admin',
 *          'name' => esc_html__( 'Admin', 'awethemes' ),
 *      ),
 *      ...
 * )
 *
 * Icons (required):
 *
 * array(
 *      array(
 *          'id'    => 'dashicons-admin-comments',
 *          'name'  => esc_html__( 'Comments', 'awethemes' ),
 *          'group' => 'admin', // Optional, if isset.
 *      ),
 *      ...
 * )
 */
interface Iconpack_Interface {
	/**
	 * Return an array icon groups.
	 *
	 * @return array
	 */
	public function groups();

	/**
	 * Return an array of icons.
	 *
	 * @return array
	 */
	public function icons();
}
