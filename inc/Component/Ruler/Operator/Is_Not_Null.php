<?php
namespace AweBooking\Component\Ruler\Operator;

use Ruler\Context;

class Is_Not_Null extends Is_Null {
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
