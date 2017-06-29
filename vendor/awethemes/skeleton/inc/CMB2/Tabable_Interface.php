<?php
namespace Skeleton\CMB2;

interface Tabable_Interface {
	/**
	 * Set tab properties.
	 *
	 * @param  array $args The tab properties.
	 * @return $this
	 */
	public function set( $args = array() );

	/**
	 * Return tab unique ID.
	 *
	 * @return string
	 */
	public function uniqid();

	/**
	 * Checks required user capabilities and whether the theme has the
	 * feature support required by the tab.
	 *
	 * @return bool
	 */
	public function check_capabilities();
}
