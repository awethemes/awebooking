<?php
namespace AweBooking\Component\Ruler\Operator;

use Ruler\Context;
use Ruler\Proposition;
use Ruler\Operator\VariableOperator;

class Is_Null extends VariableOperator implements Proposition {
	/**
	 * Evaluate the operands.
	 *
	 * @param  Context $context Context with which to evaluate this Proposition.
	 * @return boolean
	 */
	public function evaluate( Context $context ) {
		/* @var \Ruler\Variable $left */
		list( $left ) = $this->getOperands();

		return null === $left->prepareValue( $context )->getValue();
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
