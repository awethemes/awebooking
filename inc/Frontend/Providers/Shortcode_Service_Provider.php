<?php
namespace AweBooking\Frontend\Providers;

use AweBooking\Constants;
use AweBooking\Support\Service_Provider;

class Shortcode_Service_Provider extends Service_Provider {
	/**
	 * Init (boot) the service provider.
	 *
	 * @return void
	 */
	public function init() {
		$shortcodes = apply_filters( 'awebooking/shortcodes', [
			'awebooking_search_form'        => \AweBooking\Frontend\Shortcodes\Search_Form_Shortcode::class,
			'awebooking_search_results'     => \AweBooking\Frontend\Shortcodes\Search_Results_Shortcode::class,
			'awebooking_check_availability' => \AweBooking\Frontend\Shortcodes\Search_Results_Shortcode::class, // Deprecated.
			'awebooking_checkout'           => \AweBooking\Frontend\Shortcodes\Checkout_Shortcode::class,
		]);

		foreach ( $shortcodes as $tag => $class ) {
			add_shortcode( $tag, $this->shortcode_callback( $class ) );
		}
	}

	/**
	 * Returns the shortcode callback.
	 *
	 * @param  string $class The shortcode class name.
	 * @return Closure
	 */
	protected function shortcode_callback( $class ) {
		return function( $atts, $contents = '' ) use ( $class ) {
			return $this->plugin
				->makeWith( $class, compact( 'atts', 'contents' ) )
				->build();
		};
	}
}
