<?php
namespace AweBooking\Ruler\Operator;

use Ruler\Value;
use Ruler\Context;
use Ruler\Proposition;
use Ruler\Operator\VariableOperator;
use AweBooking\Support\Carbonate;

class Between extends VariableOperator implements Proposition {
	/**
	 * Evaluate the operands.
	 *
	 * @param  Context $context Context with which to evaluate this Proposition.
	 * @return boolean
	 */
	public function evaluate( Context $context ) {
		list( $left, $right ) = $this->getOperands();

		$left  = $left->prepareValue( $context )->getValue();
		$right = $right->prepareValue( $context )->getValue();

		if ( 2 !== count( $right ) ) {
			return false;
		}

		// Support date-time comparison.
		if ( $right[0] instanceof Carbonate && $right[1] instanceof Carbonate ) {
			return Carbonate::create_datetime( $left )->between( $right[0], $right[1], true );
		}

		// Make sure we have a valid range.
		if ( $right[0] > $right[1] ) {
			$temp     = $right[0];
			$right[0] = $right[1];
			$right[1] = $temp;
		}

		return ( $left >= $right[0] && $left <= $right[1] );
	}

	/**
	 * Gets the operand cardinality.
	 *
	 * @return string
	 */
	protected function getOperandCardinality() {
		return static::BINARY;
	}
}
