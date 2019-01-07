<?php

namespace AweBooking\Core\Providers;

use AweBooking\Support\Service_Provider;

class Shortcode_Service_Provider extends Service_Provider {
	/**
	 * Init (boot) the service provider.
	 *
	 * @return void
	 */
	public function init() {
		$shortcodes = apply_filters( 'abrs_shortcodes', [
			'awebooking_search_form' => \AweBooking\Core\Shortcode\Search_Form_Shortcode::class,
			'awebooking_check_form'  => \AweBooking\Core\Shortcode\Search_Form_Shortcode::class, // Alias of [awebooking_search_form].
			'awebooking_rooms'       => \AweBooking\Core\Shortcode\Rooms_Shortcode::class,
			'awebooking_single_room' => \AweBooking\Core\Shortcode\Single_Room_Shortcode::class,
		]);

		foreach ( $shortcodes as $tag => $class ) {
			add_shortcode( $tag, $this->shortcode_callback( $tag, $class ) );
		}
	}

	/**
	 * Returns the shortcode callback.
	 *
	 * @param  string $tag   The shortcode tag name.
	 * @param  string $class The shortcode class name.
	 *
	 * @return \Closure
	 */
	protected function shortcode_callback( $tag, $class ) {
		return function( $atts, $contents = '' ) use ( $tag, $class ) {
			$shortcode = $this->plugin->make( $class );

			if ( ! $shortcode->tag ) {
				$shortcode->tag = $tag;
			}

			return $shortcode->build( $atts, $contents );
		};
	}
}
