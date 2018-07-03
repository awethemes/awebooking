<?php
namespace AweBooking\Support\Traits;

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
	 * Sets the options.
	 *
	 * @return void
	 */
	public function sets_options() {
		$this->options = new Fluent( get_option( Constants::OPTION_KEY, [] ) );

		$this->original_option = $this->current_option = Constants::OPTION_KEY;

		do_action( 'abrs_loaded_options', $this );
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

		$option = $this->normalize_option_key( $language );

		// Prevent switch to same option name.
		if ( $this->current_option === $option ) {
			return false;
		}

		$this->switched_options[] = $option;

		$this->change_options( $option );

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

		$this->change_options( $option );

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
	 * Change the options to another language.
	 *
	 * @param  string $language The language to change to.
	 * @return void
	 */
	public function change_options( $language ) {
		if ( ! abrs_running_on_multilanguage() ) {
			return;
		}

		$this->current_option = $this->normalize_option_key( $language );

		$this->options = new Fluent( get_option( $this->current_option, [] ) );

		do_action( 'abrs_change_options', $this );
	}

	/**
	 * Normalize the option name according to the given language.
	 *
	 * @param  string $language The language name (2 letters).
	 * @return string
	 */
	public function normalize_option_key( $language ) {
		if ( empty( $language ) || in_array( $language, [ 'en', 'all', 'default', 'original' ] ) ) {
			return Constants::OPTION_KEY;
		}

		return  Constants::OPTION_KEY . '_' . trim( $language );
	}
	/**
	 *
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
	 * Gets the options.
	 *
	 * @return \AweBooking\Support\Fluent
	 */
	public function get_options() {
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

		// Retrieve the option value.
		$value = abrs_esc_option( $option,
			maybe_unserialize( $this->options->get( $option, $default ) )
		);

		/**
		 * Filters the value of an existing option.
		 *
		 * @param mixed  $value  Value of the option.
		 * @param string $option Option name.
		 */
		return apply_filters( "abrs_option_{$option}", $value, $option );
	}
}
