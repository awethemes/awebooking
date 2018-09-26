<?php

namespace Awethemes\Relationships\Direction;

use Awethemes\Relationships\Relationship;

class Reciprocal implements Direction {
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
		return Relationship::DIRECTION_ANY;
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_directed_class() {
		return Indeterminate_Directed::class;
	}
}
