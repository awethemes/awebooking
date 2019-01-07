<?php

namespace AweBooking\Core\Concerns;

use AweBooking\Constants;
use AweBooking\Support\Fluent;

trait Plugin_Options {
	/**
	 * The current plugin options.
	 *
	 * @var \AweBooking\Support\Fluent
	 */
	protected $options;

	/**
	 * The current plugin options key.
	 *
	 * @var string
	 */
	protected $current_option;

	/**
	 * The original plugin options.
	 *
	 * @var \AweBooking\Support\Fluent
	 */
	protected $original_options;

	/**
	 * The original plugin options key.
	 *
	 * @var string
	 */
	protected $original_option;

	/**
	 * Cache the switched options keys.
	 *
	 * @var array
	 */
	protected $switched_options = [];

	/**
	 * Change the options to another option.
	 *
	 * @param  string $language The language to switch to.
	 * @return void
	 */
	public function change_options( $language ) {
		if ( abrs_running_on_multilanguage() ) {
			$this->set_options( abrs_normalize_option_name( $language ) );
		}
	}

	/**
	 * Switches the options according to the given language.
	 *
	 * @param  string $language The language to switch to.
	 * @return bool
	 */
	public function switch_to_options( $language ) {
		if ( ! abrs_running_on_multilanguage() ) {
			return false;
		}

		$option = abrs_normalize_option_name( $language );

		// Prevent switch to same option name.
		if ( $this->current_option === $option ) {
			return false;
		}

		$this->switched_options[] = $option;

		$this->set_options( $option );

		/**
		 * Fires when the options is switched.
		 *
		 * @param string $lang The new option name.
		 */
		do_action( 'abrs_switch_options', $option, $this );

		return true;
	}

	/**
	 * Restores the options according to the previous option.
	 *
	 * @return string|false
	 */
	public function restore_previous_options() {
		$previous_options = array_pop( $this->switched_options );

		// The stack is empty, bail.
		if ( null === $previous_options ) {
			return false;
		}

		$option = end( $this->switched_options );

		// There's nothing left in the stack: go back to the original option.
		if ( ! $option ) {
			$option = $this->original_option;
		}

		$this->set_options( $option );

		/**
		 * Fires when the option is restored to the previous one.
		 *
		 * @param string $option           The new option.
		 * @param string $previous_options The previous options.
		 */
		do_action( 'abrs_restore_previous_options', $option, $previous_options );

		return $option;
	}

	/**
	 * Restores the options according to the original option.
	 *
	 * @return string|false
	 */
	public function restore_original_options() {
		if ( empty( $this->switched_options ) ) {
			return false;
		}

		$this->switched_options = [ $this->original_option ];

		return $this->restore_previous_options();
	}

	/**
	 * Whether static::switch_to_options() is in effect.
	 *
	 * @return bool
	 */
	public function is_options_switched() {
		return ! empty( $this->switched_options );
	}

	/**
	 * Sets the options using new option key name.
	 *
	 * @param string $option The new option key name.
	 */
	public function set_options( $option ) {
		$this->options = new Fluent( get_option( $option, [] ) );

		$this->current_option = $option;

		do_action( 'abrs_change_options', $this );
	}

	/**
	 * Sets the options.
	 *
	 * @return void
	 */
	public function retrieve_options() {
		$this->options          = new Fluent( get_option( $option = Constants::OPTION_KEY, [] ) );
		$this->original_options = clone $this->options;

		$this->current_option = $this->original_option = $option;

		do_action( 'abrs_retrieved_options', $this );
	}

	/**
	 * Gets the current option key name.
	 *
	 * @return string
	 */
	public function get_current_option() {
		return $this->current_option;
	}

	/**
	 * Gets the original option key name.
	 *
	 * @return string
	 */
	public function get_original_option() {
		return $this->original_option;
	}

	/**
	 * Gets the current options.
	 *
	 * @return \AweBooking\Support\Fluent
	 */
	public function get_options() {
		if ( ! did_action( 'after_setup_theme' ) ) {
			_doing_it_wrong(
				__CLASS__ . '::' . __FUNCTION__,
				esc_html__( 'Get options should be called after the `after_setup_theme` action.', 'awebooking' ),
				'3.1.0'
			);

			return new Fluent( [] );
		}

		$options = ! is_null( $this->options ) ? $this->options : new Fluent;

		return apply_filters( 'abrs_get_options', $options, $this );
	}

	/**
	 * Retrieves an option by key-name.
	 *
	 * @param  string $option  Option key name.
	 * @param  mixed  $default The default value.
	 * @return mixed
	 */
	public function get_option( $option, $default = null ) {
		static $translatable_options;

		/**
		 * Filters the value of an existing option before it is retrieved.
		 *
		 * @param mixed  $pre_option The value to return instead of the option value.
		 * @param string $option     The option name.
		 * @param mixed  $default    The fallback value to return if the option does not exist.
		 */
		$pre = apply_filters( "abrs_pre_option_{$option}", null, $option, $default );

		if ( null !== $pre ) {
			return $pre;
		}

		// Determines should get the options or original options?
		$options = $this->get_options();

		if ( abrs_running_on_multilanguage() && $this->original_option !== $this->current_option ) {
			if ( is_null( $translatable_options ) ) {
				$translatable_options = abrs_get_translatable_options();
			}

			if ( ! in_array( $option, (array) $translatable_options ) ) {
				$options = $this->original_options;
			}
		}

		// Retrieve the option value.
		$value = maybe_unserialize( $options->get( $option, $default ) );

		/**
		 * Filters the value of an existing option.
		 *
		 * @param mixed  $value  Value of the option.
		 * @param string $option Option name.
		 */
		return apply_filters( "abrs_option_{$option}", $value, $option );
	}
}
