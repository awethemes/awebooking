<?php
namespace AweBooking\Ruler\Operator;

use Ruler\Context;

class Not_Ends_With extends Ends_With {
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
