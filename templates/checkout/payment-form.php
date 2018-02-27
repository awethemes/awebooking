<?php
/**
 * Checkout payment section.
 *
 * @author  awethemes
 * @package AweBooking/Templates
 * @version 3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$request = awebooking()->make( 'request' );
$gateways = awebooking( 'gateways' )->enabled();

?><div id="payment" class="awebooking-checkout-payment">
	<ul>
		<?php
		if ( $gateways->isNotEmpty() ) {
			foreach ( $gateways as $gateway ) {
				awebooking( 'template' )->get( 'checkout/payment-method.php', compact( 'gateway' ) );
			}
		} else {
			// ...
		}
		?>
	</ul>
</div><!-- #payment -->
