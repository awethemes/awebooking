<?php
namespace AweBooking\Http\Controllers;

use Awethemes\Http\Response;

abstract class Controller {
	/**
	 * Return a new response or a response factory.
	 *
	 * @param  string|null $content  The response content.
	 * @param  integer     $status   The response status.
	 * @param  array       $headers  The response headers.
	 * @return \Awethemes\Http\Response
	 */
	protected function response( $content = null, $status = 200, array $headers = [] ) {
		$response = awebooking()->make( 'response' );

		return is_null( $content ) ? $response : $response->create( $content, $status, $headers );
	}

	/**
	 * Get instance of the Redirector.
	 *
	 * @param  string $url Optional URL to redirect.
	 * @return \AweBooking\Http\Routing\Redirector|\AweBooking\Http\Redirect_Response
	 */
	protected function redirect( $url = null ) {
		$redirector = awebooking()->make( 'redirector' );

		return $url ? $redirector->to( $url ) : $redirector;
	}

	/**
	 * Get the `flash_message` instance.
	 *
	 * @param  string $type    Optional, the notice type if provided: 'updated', 'success', 'info', 'error', 'warning'.
	 * @param  string $message Optional, the notice message.
	 * @return \AweBooking\Support\Flash_Message
	 */
	protected function notices( $type = null, $message = '' ) {
		$notices = awebooking( 'flash_message' );

		if ( is_null( $type ) ) {
			return $notices;
		}

		return $notices->add_message( $message, $type );
	}
}
