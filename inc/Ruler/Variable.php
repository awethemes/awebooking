<?php
namespace AweBooking\Ruler;

use Ruler\VariableOperand;
use Ruler\Operator\VariableOperator;
use Ruler\Variable as Ruler_Variable;

class Variable extends Ruler_Variable implements \ArrayAccess {
	/**
	 * The variable properties.
	 *
	 * @var array
	 */
	protected $properties = [];

	/**
	 * An array of mapping rules operators.
	 *
	 * @var array
	 */
	protected $operators = [
		// Mathematical operators.
		'add'                   => \Ruler\Operator\Addition::class,
		'subtract'              => \Ruler\Operator\Subtraction::class,
		'multiply'              => \Ruler\Operator\Multiplication::class,
		'divide'                => \Ruler\Operator\Division::class,
		'modulo'                => \Ruler\Operator\Modulo::class,
		'exponentiate'          => \Ruler\Operator\Exponentiate::class,
		'negate'                => \Ruler\Operator\Negation::class,
		'ceil'                  => \Ruler\Operator\Ceil::class,
		'floor'                 => \Ruler\Operator\Floor::class,

		// Array (Set) operators.
		'min'                   => \Ruler\Operator\Min::class,
		'max'                   => \Ruler\Operator\Max::class,
		'union'                 => \Ruler\Operator\Union::class,
		'intersect'             => \Ruler\Operator\Intersect::class,
		'complement'            => \Ruler\Operator\Complement::class,
		'symmetric_Difference'  => \Ruler\Operator\SymmetricDifference::class,

		// Compare operators.
		'equal'                 => \AweBooking\Ruler\Operator\Equal::class,
		'not_equal'             => \AweBooking\Ruler\Operator\Not_Equal::class,
		'in'                    => \AweBooking\Ruler\Operator\In::class,
		'not_in'                => \AweBooking\Ruler\Operator\Not_In::class,
		'less_than'             => \AweBooking\Ruler\Operator\Less_Than::class,
		'less_than_or_equal'    => \AweBooking\Ruler\Operator\Less_Than_Or_Equal::class,
		'greater_than'          => \AweBooking\Ruler\Operator\Greater_Than::class,
		'greater_than_or_equal' => \AweBooking\Ruler\Operator\Greater_Than_Or_Equal::class,
		'between'               => \AweBooking\Ruler\Operator\Between::class,
		'not_between'           => \AweBooking\Ruler\Operator\Not_Between::class,
		'begins_with'           => \AweBooking\Ruler\Operator\Starts_With::class,
		'not_begins_with'       => \AweBooking\Ruler\Operator\Not_Starts_With::class,
		'contains'              => \AweBooking\Ruler\Operator\String_Contains::class,
		'not_contains'          => \AweBooking\Ruler\Operator\String_Does_Not_Contain::class,
		'ends_with'             => \AweBooking\Ruler\Operator\Ends_With::class,
		'not_ends_with'         => \AweBooking\Ruler\Operator\Not_Ends_With::class,

		// Check operators.
		'is_empty'              => \AweBooking\Ruler\Operator\Is_Empty::class,
		'is_not_empty'          => \AweBooking\Ruler\Operator\Is_Not_Empty::class,
		'is_null'               => \AweBooking\Ruler\Operator\Is_Null::class,
		'is_not_null'           => \AweBooking\Ruler\Operator\Is_Not_Null::class,
	];

	/**
	 * Handle dynamic calls to the operators.
	 *
	 * @param  string $method     The method name.
	 * @param  array  $parameters The method parameters.
	 * @return mixed
	 *
	 * @throws \BadMethodCallException
	 */
	public function __call( $method, $parameters ) {
		if ( ! array_key_exists( $method, $this->operators ) ) {
			throw new \BadMethodCallException( "The [{$method}] operator is not supported." );
		}

		$class = $this->operators[ $method ];

		$parameters = array_map( function( $var ) {
			return $this->as_variable( $var );
		}, $parameters );

		// Create new operator instance.
		$operator = new $class( $this, ...$parameters );

		return ( $operator instanceof VariableOperand )
			? $this->wrap_variable( $operator )
			: $operator;
	}

	/**
	 * Retrieve a Variable instance for the given variable.
	 *
	 * @param   mixed $variable The variable instance or value.
	 * @return \Ruler\Variable
	 */
	protected function as_variable( $variable ) {
		return ( $variable instanceof Ruler_Variable ) ? $variable : new Ruler_Variable( null, $variable );
	}

	/**
	 * Wrap a VariableOperator in a Variable instance.
	 *
	 * @param  \Ruler\Operator\VariableOperator $op The VariableOperator instance.
	 * @return \Ruler\Variable
	 */
	protected function wrap_variable( VariableOperator $op ) {
		return new static( null, $op );
	}

	/**
	 * Get a property (create new if not exists).
	 *
	 * @param  string $name  The property name.
	 * @param  mixed  $value The default value.
	 * @return \AweBooking\Ruler\Variable_Property
	 */
	public function get_property( $name, $value = null ) {
		if ( ! array_key_exists( $name, $this->properties ) ) {
			$this->properties[ $name ] = new Variable_Property( $this, $name, $value );
		}

		return $this->properties[ $name ];
	}

	/**
	 * Set a property value.
	 *
	 * @param  string $name  The property name.
	 * @param  mixed  $value The property value.
	 * @return \AweBooking\Ruler\Variable_Property
	 */
	public function set_property( $name, $value ) {
		$property = $this->get_property( $name );

		$property->setValue( $value );

		return $property;
	}

	/**
	 * Determine if the given offset exists.
	 *
	 * @param  string $offset The offset key.
	 * @return bool
	 */
	public function offsetExists( $offset ) {
		return isset( $this->properties[ $offset ] );
	}

	/**
	 * Get the value for a given offset.
	 *
	 * @param  string $offset The offset key.
	 * @return mixed
	 */
	public function offsetGet( $offset ) {
		return $this->get_property( $offset );
	}

	/**
	 * Set the value at the given offset.
	 *
	 * @param  string $offset The offset key.
	 * @param  mixed  $value  The offset value.
	 * @return void
	 */
	public function offsetSet( $offset, $value ) {
		$this->set_property( $offset, $value );
	}

	/**
	 * Unset the value at the given offset.
	 *
	 * @param  string $offset The offset key.
	 * @return void
	 */
	public function offsetUnset( $offset ) {
		unset( $this->properties[ $offset ] );
	}
}
