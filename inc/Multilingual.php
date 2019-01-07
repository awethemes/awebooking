<?php

namespace AweBooking;

class Multilingual {
	/**
	 * The current language.
	 *
	 * @var string
	 */
	protected $current_language;

	/**
	 * The default language.
	 *
	 * @var string
	 */
	protected $default_language;

	/**
	 * Constructor.
	 */
	public function __construct() {
		// Make sure to create class after/within 'setup_theme' action.
		if ( ! did_action( 'setup_theme' ) ) {
			_doing_it_wrong(
				__CLASS__ . '::' . __FUNCTION__,
				'The class must be call after "setup_theme" has been fire.',
				'3.10'
			);
		} else {
			$this->check();
		}
	}

	/**
	 * Get the current language.
	 *
	 * @return string|null
	 */
	public function get_current_language() {
		return $this->current_language ?: null;
	}

	/**
	 * Get the main language.
	 *
	 * @return string|null
	 */
	public function get_default_language() {
		return $this->default_language ?: null;
	}

	/**
	 * Get the original post.
	 *
	 * @param  int $post_id The post ID.
	 * @return int
	 */
	public function get_original_post( $post_id ) {
		return $this->get_original_object( $post_id, 'post' );
	}

	/**
	 * Get the original object ID (post, taxonomy, etc...).
	 *
	 * @param  int    $id   The object id.
	 * @param  string $type Optional, post type or taxonomy name of the object, defaults to 'post'.
	 * @return int|null
	 */
	public function get_original_object( $id, $type = 'post' ) {
		return icl_object_id( $id, $type, true, $this->get_default_language() );
	}

	/**
	 * Perform check the language.
	 *
	 * @access private
	 */
	public function check() {
		switch ( true ) {
			case ( static::is_wpml() ):
				global $sitepress;
				$this->current_language = $sitepress->get_current_language();
				$this->default_language = $sitepress->get_default_language();
				break;

			case ( static::is_polylang() ):
				$this->default_language = pll_default_language( 'slug' );
				$this->current_language = pll_current_language( 'slug' );
				break;
		}
	}

	/**
	 * Sets the specified language.
	 *
	 * @param  string|null $language The language name.
	 * @return void
	 */
	public function set_language( $language = null ) {
		if ( static::is_polylang() ) {
			$this->set_polylang_language( $language );
		} elseif ( static::is_wpml() ) {
			global $sitepress;
			$sitepress->switch_lang( $language, ! headers_sent() );
		}
	}

	/**
	 * Sets the specified language on PLL.
	 *
	 * @sse \PLL_Choose_Lang::set_language()
	 *
	 * @param  string|null $language The language name.
	 * @return void
	 */
	public function set_polylang_language( $language = null ) {
		if ( ! static::is_polylang() ) {
			return;
		}

		/* @var \Polylang $polylang */
		$polylang = PLL();

		// In frontend, if no language given, get the preferred language
		// according to the browser preferences.
		if ( empty( $language ) && ( ! is_admin() && ! defined( 'DOING_CRON' ) ) ) {
			$curlang = $polylang->choose_lang->get_preferred_language();
		} else {
			$curlang = $polylang->model->get_language( trim( $language ) );
		}

		if ( $curlang instanceof \PLL_Language ) {
			$polylang->curlang = $curlang;
			$GLOBALS['text_direction'] = $curlang->is_rtl ? 'rtl' : 'ltr'; // @codingStandardsIgnoreLine
		}
	}

	/**
	 * Determine if we're using WPML.
	 *
	 * Since PolyLang has a compatibility layer for WPML, we'll have to consider that too.
	 *
	 * @return bool
	 */
	public static function is_wpml() {
		return ( defined( 'ICL_SITEPRESS_VERSION' ) && ! static::is_polylang() );
	}

	/**
	 * Determine if we're using PolyLang.
	 *
	 * @return bool
	 */
	public static function is_polylang() {
		return class_exists( 'Polylang' ) && function_exists( 'pll_current_language' );
	}
}
