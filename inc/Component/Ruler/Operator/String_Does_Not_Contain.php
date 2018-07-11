<?php
namespace AweBooking\Component\Ruler\Operator;

use Ruler\Context;
use Ruler\Operator\StringDoesNotContain as Base_Operator;

class String_Does_Not_Contain extends Base_Operator {
	/**
	 * Evaluate the operands.
	 *
	 * @param  Context $context Context with which to evaluate this Proposition.
	 * @return boolean
	 */
	public function evaluate( Context $context ) {
		return parent::evaluate( $context );
	}
}
