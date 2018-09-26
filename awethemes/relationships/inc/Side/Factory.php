<?php
namespace Awethemes\Relationships\Side;

class Factory {
	/**
	 * Create the object based on object name (post, user, etc.).
	 *
	 * @param  string $type The object name.
	 * @param  array  $args The side args.
	 *
	 * @return \Awethemes\Relationships\Side\Side
	 */
	public function create( $type, $args ) {
		$class_name = __NAMESPACE__ . '\\' . ucfirst( $type ) . '_Side';

		if ( ! class_exists( $class_name ) ) {
			throw new \InvalidArgumentException( "The class '{$class_name}' does not exist" );
		}

		return new $class_name( $args );
	}
}
