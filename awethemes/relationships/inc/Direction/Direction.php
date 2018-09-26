<?php

namespace Awethemes\Relationships\Direction;

interface Direction {
	/**
	 * Gets the arrow.
	 *
	 * @return string
	 */
	public function get_arrow();

	/**
	 * Chosse direction.
	 *
	 * @param  string $direction The direction.
	 * @return string
	 */
	public function choose_direction( $direction );

	/**
	 * Returns the directed class.
	 *
	 * @return string
	 */
	public function get_directed_class();
}
