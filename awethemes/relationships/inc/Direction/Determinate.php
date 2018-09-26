<?php
namespace Awethemes\Relationships\Direction;

class Determinate implements Direction {
	/**
	 * {@inheritdoc}
	 */
	public function get_arrow() {
		return '&rarr;';
	}

	/**
	 * {@inheritdoc}
	 */
	public function choose_direction( $direction ) {
		return $direction;
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_directed_class() {
		return Directed::class;
	}
}
