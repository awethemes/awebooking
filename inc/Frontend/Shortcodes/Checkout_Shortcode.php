<?php
namespace AweBooking\Frontend\Shortcodes;

class Checkout_Shortcode extends Shortcode_Abstract {
	/**
	 * {@inheritdoc}
	 */
	public function output( $request ) {
		abrs_get_template( 'checkout/checkout.php', compact( 'request' ) );
	}
}
