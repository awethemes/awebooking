<?php
namespace AweBooking\Component\Validation;

class Validator extends \Valitron\Validator {
	/**
	 * The initial rules provided.
	 *
	 * @var array
	 */
	protected $initial_rules;

	/**
	 * Create a validator.
	 *
	 * @param array $data  An array data.
	 * @param array $rules Array rules set.
	 */
	public function __construct( array $data, array $rules = [] ) {
		parent::__construct( $data, [], 'lang', __DIR__ );

		if ( $this->initial_rules = $rules ) {
			$this->add_rules( $this->initial_rules );
		}
	}

	/**
	 * Determine if the data passes the validation rules.
	 *
	 * @return bool
	 */
	public function passes() {
		return $this->validate();
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
	 * Add a single validation rule.
	 *
	 * @param  string       $name  The field name.
	 * @param  array|string $rules The rules.
	 * @return $this
	 */
	public function add_rule( $name, $rules ) {
		$parsed = ( new Parser )->parse( $rules );

		foreach ( $parsed as $rule ) {
			$this->rule( $rule[0], $name, ...$rule[1] );
		}

		return $this;
	}

	/**
	 * Sets a multiple rules for the validation.
	 *
	 * @param  array $rules Array rules.
	 * @return $this
	 */
	public function add_rules( array $rules ) {
		foreach ( $rules as $name => $rule ) {
			$this->add_rule( $name, $rule );
		}

		return $this;
	}

	/**
	 * Register new validation rule callback.
	 *
	 * @param string   $name     The rule name.
	 * @param callable $callback The callback.
	 * @param string   $message  The message.
	 *
	 * @throws \InvalidArgumentException
	 */
	public static function extend( $name, callable $callback, $message = null ) {
		static::addRule( $name, $callback, $message );
	}
}
