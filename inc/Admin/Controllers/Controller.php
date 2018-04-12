<?php
namespace AweBooking\Admin\Controllers;

use Awethemes\Http\Response;
use Awethemes\Http\Exception\AccessDeniedHttpException;

abstract class Controller {
	/**
	 * Get instance of the Redirector.
	 *
	 * @param  string $url Optional URL to redirect.
	 * @return \AweBooking\Http\Routing\Redirector|\AweBooking\Http\Redirect_Response
	 */
	protected function redirect( $url = null ) {
		$redirector = awebooking()->make( 'redirector' );

		return $url ? $redirector->admin( $url ) : $redirector;
	}

	/**
	 * Create a response of a page.
	 *
	 * @param  string  $page    The page template.
	 * @param  array   $vars    The data inject to template.
	 * @param  integer $status  The response status.
	 * @param  array   $headers The response headers.
	 * @return \Awethemes\Http\Response
	 */
	protected function response( $page, $vars = [], $status = 200, $headers = [] ) {
		$content = abrs_admin_template()->page( $page, $vars );

		return new Response( $content, $status, $headers );
	}

	/**
	 * Check the current user has a specific capability.
	 *
	 * @param  string $capability Capability name.
	 * @return void
	 *
	 * @throws AccessDeniedHttpException
	 */
	protected function require_capability( $capability ) {
		if ( ! current_user_can( $capability ) ) {
			throw new AccessDeniedHttpException( esc_html__( 'Sorry, you are not allowed to access this page.', 'awebooking' ) );
		}
	}
}
