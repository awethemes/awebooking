<?php
namespace AweBooking\Component\Validation;

class Parser {
	/**
	 * Some rules must be passed an array as first parameter.
	 *
	 * @var array
	 */
	public static $array_wrap = [ 'in', 'notin', 'between', 'creditcard' ];

	/**
	 * Parse the rules into an array of rules.
	 *
	 * @param  string|array $rules The initial rules set.
	 * @return array
	 */
	public function parse( $rules ) {
		if ( is_array( $rules ) ) {
			return $this->parse_array_rules( $rules );
		}

		return $this->parse_string_rules( $rules );
	}

	/**
	 * Explode the rules into an array of rules.
	 *
	 * @param  array $rules initial rules set.
	 * @return array
	 */
	public function explode( $rules ) {
		if ( empty( $rules ) ) {
			return [];
		}

		foreach ( $rules as $key => $_rules ) {
			$rules[ $key ] = $this->parse( $_rules );
		}

		return $rules;
	}

	/**
	 * Normalize a rule name.
	 *
	 * @param  string $rule Original rule name.
	 * @return string
	 */
	public function normalize( $rule ) {
		$rule = ucwords( str_replace( [ '-', '_' ], ' ', $rule ) );
		$rule = lcfirst( str_replace( ' ', '', $rule ) );

		switch ( $rule ) {
			case 'creditcard':
				return 'creditCard';
			case 'int':
				return 'integer';
			case 'bool':
				return 'boolean';
			default:
				return $rule;
		}
	}

	/**
	 * Parse an array based rule.
	 *
	 * @param  array $rules Array rules set.
	 * @return array
	 */
	protected function parse_array_rules( array $rules ) {
		$parse_rules = [];

		foreach ( $rules as $key => $value ) {
			if ( is_int( $key ) ) {
				$parse_rules[] = [ $this->normalize( $value ), [] ];
				continue;
			}

			$key = $this->normalize( $key );

			if ( ! is_array( $value ) ) {
				$value = [ $value ];
			}

			$parse_rules[] = [ $key, $this->wrap_parameters( $key, $value ) ];
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
		$parse_rules = [];

		foreach ( $split_rules as $rule ) {
			$parameters = [];

			// Format rule and parameters follows {rule}:{parameters}.
			if ( strpos( $rule, ':' ) !== false ) {
				list( $rule, $parameter ) = explode( ':', $rule, 2 );

				$parameter = false !== strpos( $parameter, ',' )
					? str_getcsv( $parameter )
					: [ $parameter ];

				$parameters = $this->wrap_parameters( $rule, $parameter );
			}

			$parse_rules[] = [ $this->normalize( $rule ), $parameters ];
		}

		return $parse_rules;
	}

	/**
	 * Maybe wrap the parameter in an array.
	 *
	 * @param  string $rule      The rule name.
	 * @param  mixed  $parameter The rule parameter.
	 * @return array
	 */
	protected function wrap_parameters( $rule, $parameter ) {
		return in_array( strtolower( $rule ), static::$array_wrap )
			? [ $parameter ]
			: $parameter;
	}
}
