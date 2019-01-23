<?php
namespace AweBooking\Component\Http\Middleware;

use WPLibs\Http\Request;

class Setup_Admin_Screen {
	/**
	 * Handle the incoming request.
	 *
	 * @param \WPLibs\Http\Request $request The request instance.
	 * @param \Closure                $next    Next.
	 *
	 * @return \Closure
	 */
	public function handle( Request $request, $next ) {
		if ( ! is_admin() ) {
			return $next( $request );
		}

		if ( $current_screen = get_current_screen() ) {
			$request_uri = '/' . trim( rawurldecode( $request->get( 'awebooking' ) ), '/' );

			// This action is not needed.
			remove_action( 'admin_head', 'wp_admin_canonical_url' );

			$current_screen->base = 'awebooking_route';
			$current_screen->id   = 'awebooking' . $request_uri;
		}

		return $next( $request );
	}
}
