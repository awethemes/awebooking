<?php
namespace Awethemes\Relationships\Direction;

use Awethemes\Relationships\Relationship;

class Indeterminate implements Direction {
	/**
	 * {@inheritdoc}
	 */
	public function get_arrow() {
		return '&harr;';
	}

	/**
	 * {@inheritdoc}
	 */
	public function choose_direction( $direction ) {
		return Relationship::DIRECTION_FROM;
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_directed_class() {
		return Indeterminate_Directed::class;
	}
}
