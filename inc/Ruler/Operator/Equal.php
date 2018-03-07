<?php
namespace AweBooking\Ruler\Operator;

use Ruler\Context;
use Ruler\Operator\EqualTo as Base_Operator;
use AweBooking\Support\Carbonate;

class Equal extends Base_Operator {
	/**
	 * Evaluate the operands.
	 *
	 * @param  Context $context Context with which to evaluate this Proposition.
	 * @return boolean
	 */
	public function evaluate( Context $context ) {
		list( $left, $right ) = $this->getOperands();

		$left  = $left->prepareValue( $context );
		$right = $right->prepareValue( $context );

		// Support date-time comparison.
		if ( $right->getValue() instanceof Carbonate ) {
			return Carbonate::create_datetime( $left->getValue() )->equalTo( $right->getValue() );
		}

		return parent::evaluate( $context );
	}
}
