<?php

namespace Skeleton\Support;

use Valitron\Validator as Valitron;

class Validator {
	/**
	 * The data under validation.
	 *
	 * @var array
	 */
	protected $data;

	/**
	 * The rules to be applied to the data.
	 *
	 * @var array
	 */
	protected $rules;

	/**
	 * The initial rules provided.
	 *
	 * @var array
	 */
	protected $initial_rules;

	/**
	 * Valitron Validator instance.
	 *
	 * @var \Valitron\Validator
	 */
	protected $valitron;

	/**
	 * Create a validator.
	 *
	 * @param array $data  An array data.
	 * @param array $rules Array rules set.
	 */
	public function __construct( array $data, array $rules ) {
		$this->data = $data;
		$this->initial_rules = $rules;

		$this->valitron = new Valitron( $data, array(), 'validation', dirname( __FILE__ ) . '/../../i18n' );
		$this->set_rules( $rules );
	}

	/**
	 * Determine if the data passes the validation rules.
	 *
	 * @return bool
	 */
	public function passes() {
		return $this->valitron->validate();
	}

	/**
	 * Determine if the data fails the validation rules.
	 *
	 * @return bool
	 */
	public function fails() {
		return ! $this->passes();
	}

	/**
	 * Determine if a field has any errors.
	 *
	 * @param  null|string $field Field ID.
	 * @return boolean
	 */
	public function has_errors( $field ) {
		return false !== $this->errors( $field );
	}

	/**
	 * Set rules for validation.
	 *
	 * @param  array $rules Array rules.
	 * @return $this
	 */
	protected function set_rules( array $rules ) {
		$this->rules = $this->explode_rules( $rules );

		foreach ( $this->rules as $attribute => $rules ) {
			foreach ( $rules as $ruleset ) {
				$parameters = array_merge( array( $ruleset[0], $attribute ), $ruleset[1] );
				call_user_func_array( array( $this->valitron, 'rule' ), $parameters );
			}
		}

		return $this;
	}

	/**
	 * Explode the rules into an array of rules.
	 *
	 * @param  array $rules initial rules set.
	 * @return array
	 */
	protected function explode_rules( $rules ) {
		if ( empty( $rules ) ) {
			return array();
		}

		foreach ( $rules as $key => $_rules ) {
			if ( is_array( $_rules ) ) {
				$rules[ $key ] = $this->parse_array_rules( $_rules );
			} else {
				$rules[ $key ] = $this->parse_string_rules( $_rules );
			}
		}

		return $rules;
	}

	/**
	 * Parse an array based rule.
	 *
	 * @param  array $rules Array rules set.
	 * @return array
	 */
	protected function parse_array_rules( array $rules ) {
		$parse_rules = array();

		foreach ( $rules as $key => $value ) {
			if ( is_integer( $key ) ) {
				$parse_rules[] = array( $this->normalize_rule( $value ), array() );
			} else {
				$parse_rules[] = array( $this->normalize_rule( $key ), array( $value ) );
			}
		}

		return $parse_rules;
	}

	/**
	 * Parse a string based rules.
	 *
	 * @param  string $rules String rules set.
	 * @return array
	 */
	protected function parse_string_rules( $rules ) {
		$split_rules = explode( '|', $rules );
		$parse_rules = array();

		foreach ( $split_rules as $rule ) {
			$parameters = array();

			// Format rule and parameters follows {rule}:{parameters}.
			if ( strpos( $rule, ':' ) !== false ) {
				list( $rule, $parameter ) = explode( ':', $rule, 2 );
				$parameters = $this->parse_string_parameters( $rule, $parameter );
			}

			$parse_rules[] = array( $this->normalize_rule( $rule ), $parameters );
		}

		return $parse_rules;
	}

	/**
	 * Parse a parameter list.
	 *
	 * @param  string $rule      The rule name.
	 * @param  string $parameter The rule raw parameter.
	 * @return array
	 */
	protected function parse_string_parameters( $rule, $parameter ) {
		switch ( strtolower( $rule ) ) {
			case 'length':
			case 'lengthbetween':
			case 'creditcard': // Seem this not working.
				return str_getcsv( $parameter );
				break;

			case 'in':
			case 'notin':
				return array( str_getcsv( $parameter ) );
				break;

			default:
				return array( $parameter );
				break;
		}
	}

	/**
	 * Normalize rule name.
	 *
	 * @param  string $rule Original rule value.
	 * @return string
	 */
	protected function normalize_rule( $rule ) {
		$rule = ucwords( str_replace( array( '-', '_' ), ' ', $rule ) );
		$rule = lcfirst( str_replace( ' ', '', $rule ) );

		switch ( $rule ) {
			case 'int':
				return 'integer';
			case 'bool':
				return 'boolean';
			default:
				return $rule;
		}
	}

	/**
	 * Allow call dynamic method from Valitron.
	 *
	 * @param  string $method     Method to call.
	 * @param  array  $parameters Call parameters.
	 * @return mixed
	 */
	public function __call( $method, $parameters ) {
		if ( ! method_exists( $this->valitron, $method ) ) {
			throw new \BadMethodCallException;
		}

		return call_user_func_array( array( $this->valitron, $method ), $parameters );
	}
}
