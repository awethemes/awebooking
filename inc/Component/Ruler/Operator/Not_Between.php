<?php
namespace AweBooking\Component\Ruler\Operator;

use Ruler\Context;

class Not_Between extends Between {
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
