<?php
namespace Awethemes\Relationships\Direction;

use Awethemes\Relationships\Side\Side;

class Factory {
	/**
	 * Create the direction.
	 *
	 * @param \Awethemes\Relationships\Side\Side $from       The from side.
	 * @param \Awethemes\Relationships\Side\Side $to         The to side.
	 * @param bool                               $reciprocal Is reciprocal.
	 *
	 * @return \Awethemes\Relationships\Direction\Direction
	 */
	public function create( Side $from, Side $to, $reciprocal = false ) {
		$class_name = Determinate::class;

		if ( $from->is_same_type( $to ) && $from->is_indeterminate( $to ) ) {
			$class_name = $reciprocal ? Reciprocal::class : Indeterminate::class;
		}

		return new $class_name;
	}
}
