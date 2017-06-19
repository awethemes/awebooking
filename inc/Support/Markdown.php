<?php
namespace AweBooking\Support;

use Parsedown;

class Markdown {
	/**
	 * Parse the given Markdown text into HTML.
	 *
	 * @param  string $text Markdown text to HTML.
	 * @return string
	 */
	public static function parse( $text ) {
		if ( empty( $text ) ) {
			return '';
		}

		return (new Parsedown)->text( $text );
	}
}
