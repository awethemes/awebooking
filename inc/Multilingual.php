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
			trigger_error( 'The class must be call after "setup_theme" has been fire.' );
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
