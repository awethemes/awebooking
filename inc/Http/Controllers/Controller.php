<?php
namespace AweBooking\Http\Controllers;

use Awethemes\Http\Request;
use Skeleton\Support\Validator;

abstract class Controller {
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
}
