<?php
namespace AweBooking\Component\Ruler\Operator;

use Ruler\Context;
use Ruler\Operator\GreaterThanOrEqualTo as Base_Operator;
use AweBooking\Support\Carbonate;

class Greater_Than_Or_Equal extends Base_Operator {
	/**
	 * Evaluate the operands.
	 *
	 * @param  Context $context Context with which to evaluate this Proposition.
	 * @return boolean
	 */
	public function evaluate( Context $context ) {
		/**
		 * Extracts variables.
		 *
		 * @var \Ruler\Variable $left
		 * @var \Ruler\Variable $right
		 */
		list( $left, $right ) = $this->getOperands();

		$left  = $left->prepareValue( $context );
		$right = $right->prepareValue( $context );

		// Support date-time comparison.
		if ( $right->getValue() instanceof Carbonate ) {
			return Carbonate::create_date_time( $left->getValue() )->greaterThanOrEqualTo( $right->getValue() );
		}

		return parent::evaluate( $context );
	}
}
