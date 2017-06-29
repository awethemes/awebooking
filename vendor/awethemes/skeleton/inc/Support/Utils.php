<?php
namespace Skeleton\Support;

class Utils {
	/**
	 * Return a clone ID by given a suffix.
	 *
	 * @param  string $base_id
	 * @param  string $clone_suffix
	 * @return string
	 */
	public static function clone_id( $base_id, $clone_suffix ) {
		$id_data = Multidimensional::split( $base_id );

		if ( empty( $id_data['keys'] ) ) {
			return $base_id . '_' . $clone_suffix;
		}

		$editkey = array_pop( $id_data['keys'] );
		$id_data['keys'][] = $editkey . '_' . $clone_suffix;

		return Multidimensional::build( $id_data );
	}

	/**
	 * Guest a system path to a URL.
	 *
	 * @param  string $path Directory path to convert.
	 * @return string
	 */
	public static function guest_url( $path ) {
		return \CMB2_Utils::get_url_from_dir( $path );
	}

	/**
	 * Get image mime types.
	 *
	 * @return array
	 */
	public static function image_mime_types() {
		$mime_types = get_allowed_mime_types();

		foreach ( $mime_types as $id => $type ) {
			if ( false === strpos( $type, 'image/' ) ) {
				unset( $mime_types[ $id ] );
			}
		}

		/**
		 * Filter image mime types.
		 *
		 * @param array $mime_types Image mime types.
		 */
		return apply_filters( 'skeleton/image_mime_types', $mime_types );
	}

	/**
	 * Converts a multidimensional array of CSS rules into a CSS string.
	 *
	 * @link http://www.grasmash.com/article/convert-nested-php-array-css-string
	 *
	 * @param  array $rules   An array of CSS rules.
	 * @param  int   $_indent Private arguments, set indent size.
	 * @return string
	 */
	public static function generate_css( array $rules, $_indent = 0 ) {
		$css = '';
		$prefix = str_repeat( '  ', $_indent );

		foreach ( $rules as $key => $value ) {
			if ( is_array( $value ) ) {
				$selector = $key;
				$properties = $value;

				$css .= $prefix . "{$selector} {\n";
				$css .= $prefix . static::generate_css( $properties, $_indent + 1 );
				$css .= $prefix . "}\n";
			} else {
				$property = $key;
				$css .= $prefix . "{$property}: $value;\n";
			}
		}

		return $css;
	}

	/**
	 * Minify CSS by remove comments, whitespaces.
	 *
	 * @see https://www.w3.org/TR/CSS2/grammar.html#scanner
	 *
	 * @param  string $styles
	 * @return string
	 */
	public static function minify_css( $styles ) {
		$styles = preg_replace( '#\/\*[^*]*\*+([^/*][^*]*\*+)*\/#', '', $styles ); // Remove comments.
		$styles = preg_replace( '/\s+/', ' ', $styles ); // Remove whitespace.

		return $styles;
	}

	/**
	 * Converts a HEX value to RGB.
	 *
	 * @param  string $color The original color, in 3- or 6-digit hexadecimal form.
	 * @return array Array containing RGB (red, green, and blue) values for the given
	 *               HEX code, empty array otherwise.
	 */
	public static function hex2rgb( $color ) {
		$color = trim( $color, '#' );

		if ( strlen( $color ) === 3 ) {
			$r = hexdec( substr( $color, 0, 1 ) . substr( $color, 0, 1 ) );
			$g = hexdec( substr( $color, 1, 1 ) . substr( $color, 1, 1 ) );
			$b = hexdec( substr( $color, 2, 1 ) . substr( $color, 2, 1 ) );
		} else if ( strlen( $color ) === 6 ) {
			$r = hexdec( substr( $color, 0, 2 ) );
			$g = hexdec( substr( $color, 2, 2 ) );
			$b = hexdec( substr( $color, 4, 2 ) );
		} else {
			return array();
		}

		return array( 'red' => $r, 'green' => $g, 'blue' => $b );
	}

	/**
	 * Determine if a given string matches a given pattern.
	 *
	 * @param  string $pattern
	 * @param  string $value
	 * @return bool
	 */
	public static function str_is( $pattern, $value ) {
		if ( $pattern == $value ) {
			return true;
		}

		$pattern = preg_quote( $pattern, '#' );

		// Asterisks are translated into zero-or-more regular expression wildcards
		// to make it convenient to check if the strings starts with the given
		// pattern such as "library/*", making any string check convenient.
		$pattern = str_replace( '\*', '.*', $pattern );

		return (bool) preg_match( '#^' . $pattern . '\z#u', $value );
	}

	/**
	 * Send a download file to client.
	 *
	 * @param  string $data     Data to send to client download.
	 * @param  string $filename Download file name.
	 */
	public static function send_download( $data, $filename = null ) {
		if ( empty( $filename ) ) {
			$filename = sprintf( '%s.txt', uniqid() );
		}

		header( 'Content-Type: application/octet-stream; charset=' . get_option( 'blog_charset' ), true );
		header( 'Content-disposition: attachment; filename=' . $filename );
		header( 'Content-Transfer-Encoding: binary' );
		header( 'Pragma: no-cache' );
		header( 'Expires: 0' );

		// Print data endwith newline.
		print $data . "\n"; // WPCS: XSS OK.
		exit;
	}
}
