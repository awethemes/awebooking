<?php

namespace AweBooking\Core\Bootstrap;

class Load_Textdomain {
	/**
	 * Load localisation files.
	 *
	 * Note: the first-loaded translation file overrides any following ones if the same translation is present.
	 *
	 * Locales found in:
	 *     - WP_LANG_DIR/awebooking/awebooking-LOCALE.mo
	 *     - WP_LANG_DIR/plugins/awebooking-LOCALE.mo
	 *
	 * @param \AweBooking\Plugin $plugin The plugin instance.
	 */
	public function bootstrap( $plugin ) {
		$locale = is_admin() && function_exists( 'get_user_locale' ) ? get_user_locale() : get_locale();
		$locale = apply_filters( 'plugin_locale', $locale, 'awebooking' );

		unload_textdomain( 'awebooking' );
		load_textdomain( 'awebooking', WP_LANG_DIR . '/awebooking/awebooking-' . $locale . '.mo' );

		load_plugin_textdomain( 'awebooking', false, dirname( $plugin->plugin_basename() ) . '/languages' );
	}
}
