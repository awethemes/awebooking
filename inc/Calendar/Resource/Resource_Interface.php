<?php

namespace AweBooking\Calendar\Resource;

interface Resource_Interface {
	/**
	 * Get the resource ID.
	 *
	 * @return int
	 */
	public function get_id();

	/**
	 * Set the resource ID.
	 *
	 * @param int $id The new ID.
	 */
	public function set_id( $id );

	/**
	 * Get the resource value.
	 *
	 * @return string
	 */
	public function get_value();

	/**
	 * Set the resource value.
	 *
	 * @param int $value The new value.
	 */
	public function set_value( $value );

	/**
	 * Get the resource title.
	 *
	 * @return string
	 */
	public function get_title();

	/**
	 * Set the resource title.
	 *
	 * @param int $title The new title.
	 */
	public function set_title( $title );

	/**
	 * Get the resource description.
	 *
	 * @return string
	 */
	public function get_description();

	/**
	 * Set the resource description.
	 *
	 * @param int $description The new description.
	 */
	public function set_description( $description );
}
