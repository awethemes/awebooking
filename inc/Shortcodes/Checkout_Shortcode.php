<?php
namespace AweBooking\Shortcodes;

use Awethemes\Http\Request;
use AweBooking\Support\Utils as U;

class Checkout_Shortcode extends Shortcode {
	/**
	 * {@inheritdoc}
	 */
	public function output( Request $request ) {
		if ( $request->filled( 'received' ) ) {
			$this->output_received( $request );
		} else {
			$this->output_checkout( $request );
		}
	}

	/**
	 * Output the checkout screen.
	 *
	 * @param  \Awethemes\Http\Request $request The current request.
	 * @return void
	 */
	protected function output_checkout( Request $request ) {
		$session = awebooking()->make( 'reservation_session' );

		$reservation = U::rescue( function() use ( $session ) {
			return $session->resolve();
		});

		// Abort if empty the reservation session.
		if ( is_null( $reservation ) ) {
			return;
		}

		$session->keep();

		$this->template( 'checkout/checkout.php' );
	}
}
