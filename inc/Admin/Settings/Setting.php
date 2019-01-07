<?php

namespace AweBooking\Admin\Settings;

use WPLibs\Http\Request;

interface Setting {
	/**
	 * Get the setting ID.
	 *
	 * @return string
	 */
	public function get_id();

	/**
	 * Get the setting label.
	 *
	 * @return string
	 */
	public function get_label();

	/**
	 * Get the setting priority.
	 *
	 * @return int
	 */
	public function get_priority();

	/**
	 * Perform save setting.
	 *
	 * @param  \WPLibs\Http\Request $request The HTTP request.
	 * @return bool
	 */
	public function save( Request $request );

	/**
	 * Output this setting.
	 *
	 * @param  \WPLibs\Http\Request $request The HTTP request.
	 * @return void
	 */
	public function output( Request $request );
}
