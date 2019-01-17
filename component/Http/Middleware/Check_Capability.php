<?php

namespace AweBooking\Component\Http\Middleware;

use AweBooking\Constants;
use WPLibs\Http\Request;
use WPLibs\Http\Exception\AccessDeniedHttpException;

class Check_Capability {
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

		// Submenu with no privileges.
		global $_wp_submenu_nopriv;

		$request_uri = '/' . trim( rawurldecode( $request->get( 'awebooking' ) ), '/' );
		$segments = explode( '/', trim( $request_uri, '/' ) );

		$menu_slug = 'admin.php?awebooking=/' . $segments[0];
		if ( isset( $_wp_submenu_nopriv[ Constants::PARENT_MENU_SLUG ][ $menu_slug ] ) ) {
			throw new AccessDeniedHttpException( esc_html__( 'Sorry, you are not allowed to access this page.', 'awebooking' ) );
		}

		return $next( $request );
	}
}
