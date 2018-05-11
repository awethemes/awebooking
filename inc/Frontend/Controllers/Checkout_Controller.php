<?php
namespace AweBooking\Frontend\Controllers;

use AweBooking\Constants;
use Awethemes\Http\Request;
use AweBooking\Frontend\Checkout\Checkout;

class Checkout_Controller {
	/**
	 * Handle checkout.
	 *
	 * @param  \Awethemes\Http\Request $request The current request.
	 * @return \Awethemes\Http\Response
	 */
	public function checkout( Request $request, Checkout $checkout ) {
		abrs_nocache_headers();

		// Add error messages to the flash.
		// foreach ( $errors->get_error_messages() as $message ) {
		// 	abrs_add_notice( $message, 'error' );
		// }

		// return abrs_redirector()->back();

		try {
			return $checkout->process( $request );
		} catch ( \Exception $e ) {
			abrs_report( $e );
			abrs_add_notice( $e->getMessage(), 'error' );
		}

		return abrs_redirector()->back( abrs_get_page_permalink( 'checkout' ) );
	}
}
