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

		try {
			$response = $checkout->process( $request );

			dd( $response );
		} catch ( \Exception $e ) {
			esc_html__( 'We were unable to process your reservation, please try again.', 'awebooking' );
		}

		return awebooking( 'redirector' )->back();
	}
}
