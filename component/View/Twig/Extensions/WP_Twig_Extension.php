<?php

namespace AweBooking\Component\View\Twig\Extensions;

use Twig_SimpleFilter as Ft;
use Twig_SimpleFunction as Fn;

class WP_Twig_Extension extends \Twig_Extension {
	/**
	 * Returns a list of twig functions.
	 *
	 * @return array|\Twig_SimpleFunction[]
	 */
	public function getFunctions() {
		return [
			/* Theming */
			new Fn( 'wp_head', 'wp_head' ),
			new Fn( 'wp_footer', 'wp_footer' ),
			new Fn( 'body_class', 'body_class' ),

			/* Bloginfo and translate */
			new Fn( 'bloginfo', 'bloginfo' ),
			new Fn( 'translate', 'translate' ),
			new Fn( '__', '__' ),
			new Fn( '_e', '_e' ),
			new Fn( '_n', '_n' ),
			new Fn( '_x', '_x' ),
			new Fn( '_ex', '_ex' ),
			new Fn( '_nx', '_nx' ),
			new Fn( '_n_noop', '_n_noop' ),
			new Fn( '_nx_noop', '_nx_noop' ),
			new Fn( 'translate_nooped_plural', 'translate_nooped_plural' ),
		];
	}

	/**
	 * Returns a list of twig filters.
	 *
	 * @return array|\Twig_SimpleFilter[]
	 */
	public function getFilters() {
		return [
			new Ft( 'shortcodes', 'do_shortcode' ),
		];
	}
}
