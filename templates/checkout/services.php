<?php
/**
 * Output the services included.
 *
 * This template can be overridden by copying it to {yourtheme}/awebooking/checkout/services.php
 *
 * @see      http://docs.awethemes.com/awebooking/developers/theme-developers/
 * @author   awethemes
 * @package  AweBooking
 * @version  3.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<div id="checkout-services" class="checkout-services">
	<?php foreach ( $services as $service ) : ?>
		<div class="checkout-service">
			<div class="checkout-service__media">

			</div>

			<div class="checkout-service__info">
				<h3></h3>

				<div class="checkout-service__description">

				</div>
			</div>
		</div>
	<?php endif; ?>
</div>