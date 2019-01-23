<?php
namespace AweBooking\Component\Http\Middleware;

use WPLibs\Http\Request;

class Localizer {
	/**
	 * Handle the incoming request.
	 *
	 * @param \WPLibs\Http\Request $request The request instance.
	 * @param \Closure                $next    Next.
	 *
	 * @return \Closure
	 */
	public function handle( Request $request, $next ) {
		if ( abrs_running_on_multilanguage() ) {
			$multilingual = abrs_multilingual();

			// Sets the requested language.
			$multilingual->set_language( $request->get( 'lang' ) );

			// Recheck and change the settings to current language.
			$multilingual->check();
			awebooking()->change_options( $multilingual->get_current_language() );
		}

		return $next( $request );
	}
}
