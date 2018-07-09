<?php
namespace AweBooking\Component\Ruler\Operator;

use Ruler\Context;

class Is_Not_Empty extends Is_Empty {
	/**
	 * Evaluate the operands.
	 *
	 * @param  Context $context Context with which to evaluate this Proposition.
	 * @return boolean
	 */
	public function evaluate( Context $context ) {
		return ! parent::evaluate( $context );
	}
}
