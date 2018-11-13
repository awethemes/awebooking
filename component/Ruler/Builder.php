<?php

namespace AweBooking\Component\Ruler;

use Ruler\Proposition;
use Ruler\Operator\LogicalOr;
use Ruler\Operator\LogicalAnd;

class Builder implements \ArrayAccess, \Countable {
	/**
	 * Store the variables.
	 *
	 * @var array
	 */
	protected $variables = [];

	/**
	 * Create a Rule with the given propositional condition.
	 *
	 * @param  \Ruler\Proposition $condition
	 * @return \AweBooking\Component\Ruler\Rule
	 */
	public function create( Proposition $condition ) {
		return new Rule( $condition );
	}

	/**
	 * Create a logical AND operator proposition.
	 *
	 * @param  \Ruler\Proposition[] $props
	 * @return \Ruler\Operator\LogicalAnd
	 */
	public function logical_and( Proposition ...$props ) {
		return new LogicalAnd( ...$props );
	}

	/**
	 * Create a logical OR operator proposition.
	 *
	 * @param  \Ruler\Proposition[] $props
	 * @return \Ruler\Operator\LogicalOr
	 */
	public function logical_or( Proposition ...$props ) {
		return new LogicalOr( ...$props );
	}

	/**
	 * Count variables.
	 *
	 * @return int
	 */
	public function count() {
		return count( $this->variables );
	}

	/**
	 * Check whether a variable is already set.
	 *
	 * @param  string $name
	 * @return boolean
	 */
	public function offsetExists( $name ) {
		return array_key_exists( $name, $this->variables );
	}

	/**
	 * Retrieve a variable by name.
	 *
	 * @param  string $name
	 * @return Variable
	 */
	public function offsetGet( $name ) {
		if ( ! array_key_exists( $name, $this->variables ) ) {
			$this->variables[ $name ] = new Variable( $name );
		}

		return $this->variables[ $name ];
	}

	/**
	 * Set the default value of a Variable.
	 *
	 * @param  string $name
	 * @param  mixed  $value
	 */
	public function offsetSet( $name, $value ) {
		$this->offsetGet( $name )->setValue( $value );
	}

	/**
	 * Remove a defined variable.
	 *
	 * @param string $name
	 */
	public function offsetUnset( $name ) {
		unset( $this->variables[ $name ] );
	}
}
