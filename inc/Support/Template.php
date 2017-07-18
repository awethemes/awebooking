<?php
namespace AweBooking\Support;

class Template {
	/**
	 * Custom templates directory.
	 *
	 * @var array
	 */
	public static $template_dirs = [];

	/**
	 * Locate a template and return the path for inclusion.
	 *
	 * @param  string $template_name Template name.
	 * @return string
	 */
	public static function locate_template( $template_name ) {
		// Look within passed path within the theme - this is priority.
		$template = locate_template([
			// In your {theme}/awebooking.
			trailingslashit( awebooking()->template_path() ) . $template_name,
			// In your {theme}/.
			$template_name,
		]);

		if ( ! $template ) {
			$template = static::locate_custom_dirs_template( $template_name );
		}

		// Using default template in "awebooking/templates/" directory.
		if ( ! $template ) {
			$template = awebooking()->plugin_path() . '/templates/' . $template_name;
		}

		// Return what we found.
		return apply_filters( 'awebooking/locate_template', $template, $template_name );
	}

	/**
	 * Include a template by given a template name.
	 *
	 * @param string $template_name Template name.
	 * @param array  $args          Template arguments.
	 */
	public static function get_template( $template_name, array $args = array() ) {
		if ( ! empty( $args ) ) {
			extract( $args ); // @codingStandardsIgnoreLine, we need extract at here.
		}

		$located = static::locate_template( $template_name );
		if ( ! file_exists( $located ) ) {
			return;
		}

		// Allow 3rd party plugin filter template file from their plugin.
		include apply_filters( 'awebooking/get_template', $located, $template_name, $args );
	}

	/**
	 * //
	 *
	 * @param  string $template_name //.
	 * @return array
	 */
	protected static function locate_custom_dirs_template( $template_name ) {
		$template = '';

		foreach ( static::$template_dirs as $directory ) {
			$template_path = trailingslashit( $directory ) . $template_name;

			if ( file_exists( $template_path ) ) {
				$template = $template_path;
			}
		}

		return $template;
	}
}
