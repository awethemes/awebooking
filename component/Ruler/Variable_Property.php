<?php
namespace AweBooking\Component\Ruler;

use Ruler\Variable as Ruler_Variable;
use Ruler\RuleBuilder\VariablePropertyTrait;

class Variable_Property extends Variable {
	use VariablePropertyTrait;

	/**
	 * The Variable_Property class constructor.
	 *
	 * @param Ruler_Variable $parent Parent Variable instance.
	 * @param string         $name   Property name.
	 * @param mixed          $value  Default Property value (default: null).
	 */
	public function __construct( Ruler_Variable $parent, $name, $value = null ) {
		$this->setParent( $parent );

		parent::__construct( $name, $value );
	}
}
