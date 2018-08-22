<?php
namespace AweBooking\Component\Ruler;

use Ruler\Proposition;
use Ruler\Context as Ruler_Context;

class Rule implements Proposition {
	/**
	 * The rule condition.
	 *
	 * @var \Ruler\Proposition
	 */
	protected $condition;

	/**
	 * Rule constructor.
	 *
	 * @param \Ruler\Proposition $condition The condition for this Rule.
	 */
	public function __construct( Proposition $condition ) {
		$this->condition = $condition;
	}

	/**
	 * Evaluate the Rule with the given Context.
	 *
	 * @param  Ruler_Context $context Context with which to evaluate this Rule.
	 * @return boolean
	 */
	public function apply( Ruler_Context $context ) {
		return $this->evaluate( $context );
	}

	/**
	 * Evaluate the Rule with the given Context.
	 *
	 * @param  Ruler_Context $context Context with which to evaluate this Rule.
	 * @return boolean
	 */
	public function evaluate( Ruler_Context $context ) {
		return $this->condition->evaluate( $context );
	}
}
