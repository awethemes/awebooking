<?php
namespace AweBooking\Support;

class Multilingual {
	/**
	 * The active language.
	 *
	 * @var string
	 */
	protected $active_language;

	/**
	 * The "main" language.
	 *
	 * @var string
	 */
	protected $default_language;

	/**
	 * An array of all available languages.
	 *
	 * @var array
	 */
	protected $available_languages = [];

	/**
	 * Class constructor.
	 */
	public function __construct() {
	}

	public function get_original_object_id( $object_id, $object_type = 'post' ) {
		return icl_object_id( $object_id, $object_type, true, $this->get_default_language() );
	}

	/**
	 * Get the main language.
	 *
	 * @return string|null
	 */
	public function get_active_language() {
		if ( $this->active_language ) {
			return $this->active_language;
		}

		if ( $this->is_wpml() ) {
			global $sitepress;
			$this->active_language = $sitepress->get_current_language();
		} elseif ( $this->is_polylang() ) {
			$current = pll_current_language( 'slug' );
			$this->active_language = false === $current ? 'all' : $current;
		}

		return $this->active_language;
	}

	/**
	 * Get the main language.
	 *
	 * @return string|null
	 */
	public function get_default_language() {
		if ( $this->default_language ) {
			return $this->default_language;
		}

		if ( $this->is_wpml() ) {
			global $sitepress;
			$this->default_language = $sitepress->get_default_language();
		} elseif ( $this->is_polylang() ) {
			$this->default_language = pll_default_language( 'slug' );
		}

		return $this->default_language;
	}

	/**
	 * Determine if we're using PolyLang.
	 *
	 * @return bool
	 */
	public function is_polylang() {
		return class_exists( 'Polylang' ) && function_exists( 'pll_current_language' );
	}

	/**
	 * Determine if we're using WPML.
	 *
	 * Since PolyLang has a compatibility layer for WPML, we'll have to consider that too.
	 *
	 * @return bool
	 */
	public function is_wpml() {
		return ( defined( 'ICL_SITEPRESS_VERSION' ) && ! $this->is_polylang() );
	}
}
