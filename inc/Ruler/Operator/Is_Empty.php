<?php
namespace AweBooking\Ruler\Operator;

use Countable;
use Ruler\Context;
use Ruler\Proposition;
use Ruler\Operator\VariableOperator;

class Is_Empty extends VariableOperator implements Proposition {
	/**
	 * Evaluate the operands.
	 *
	 * @param  Context $context Context with which to evaluate this Proposition.
	 * @return boolean
	 */
	public function evaluate( Context $context ) {
		list( $left ) = $this->getOperands();

		$left = $left->prepareValue( $context )->getValue();

		// In case $left instance of Countable, just check count of that.
		if ( $left instanceof Countable ) {
			return count( $left ) === 0;
		}

		// If string given, check by strlen() instead empty() function.
		if ( is_string( $left ) ) {
			return strlen( $left ) === 0;
		}

		return empty( $left );
	}

	/**
	 * Gets the operand cardinality.
	 *
	 * @return string
	 */
	protected function getOperandCardinality() {
		return static::UNARY;
	}
}
