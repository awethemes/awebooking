<?php
namespace AweBooking\Component\Ruler\Operator;

use Ruler\Context;
use Ruler\Proposition;
use Ruler\Operator\VariableOperator;

class In extends VariableOperator implements Proposition {
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

		$left  = $left->prepareValue( $context )->getValue();
		$right = $right->prepareValue( $context )->getValue();

		return in_array( $left, is_array( $right ) ? $right : [ $right ] );
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
