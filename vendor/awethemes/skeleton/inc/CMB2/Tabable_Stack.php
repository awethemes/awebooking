<?php
namespace Skeleton\CMB2;

use Skeleton\Support\Priority_List;

class Tabable_Stack extends Priority_List {
	/**
	 * Make a list stack
	 *
	 * @param  array $values An array values.
	 * @return static
	 */
	public static function make( array $values ) {
		$stack = new static;

		foreach ( $values as $key => $value ) {
			$priority = is_object( $value ) ? $value->priority : $value['priority'];
			$stack->insert( $key, $value, $priority );
		}

		return $stack;
	}
}
