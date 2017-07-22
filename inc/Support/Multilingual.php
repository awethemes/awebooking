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
	protected $main_language;

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
		}

		// TODO: ...
		// Polylang support...

		return $this->active_language;
	}

	/**
	 * Get the main language.
	 *
	 * @return string|null
	 */
	public function get_default_language() {
		if ( $this->main_language ) {
			return $this->main_language;
		}

		if ( $this->is_wpml() ) {
			global $sitepress;
			$this->main_language = $sitepress->get_default_language();
		} elseif ( $this->is_polylang() ) {
			$this->main_language = pll_default_language( 'slug' );
		}

		return $this->main_language;
	}

	/**
	 * Determine if we're using PolyLang.
	 *
	 * @return bool
	 */
	public function is_polylang() {
		return defined( 'POLYLANG_VERSION' );
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
