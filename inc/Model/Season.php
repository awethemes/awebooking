<?php

namespace AweBooking\Model;

interface Season {
	/**
	 * //
	 *
	 * @return mixed
	 */
	public function get_id();

	/**
	 * //
	 *
	 * @return mixed
	 */
	public function get_name();

	/**
	 * //
	 *
	 * @return mixed
	 */
	public function get_description();

	/**
	 * //
	 *
	 * @return int
	 */
	public function get_priority();

	/**
	 * //
	 *
	 * @return mixed
	 */
	public function get_start_date();

	/**
	 * //
	 *
	 * @return string
	 */
	public function get_end_date();
}
