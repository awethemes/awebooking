<?php
namespace AweBooking\Component\Ruler;

use Ruler\Context as Ruler_Context;

class Context extends Ruler_Context {
	/**
	 * Create new context.
	 *
	 * @param  array $values The context values.
	 * @return static
	 */
	public static function create( $values ) {
		return new static( $values );
	}
}
