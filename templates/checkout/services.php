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

if ( abrs_blank( $services ) ) {
	return;
}

?>

<form id="checkout-services-form" method="POST" action="<?php echo esc_url( abrs_route( '/reservation/services' ) ); ?>">
	<div id="checkout-services" class="checkout-services">
		<h3 class="checkout-services__header"><?php esc_html_e( 'Services', 'awebooking' ); ?></h3>

		<?php foreach ( $services as $service ) : ?>
			<?php abrs_get_template( 'checkout/service-item.php', compact( 'service', 'includes' ) ); ?>
		<?php endforeach; ?>
	</div>

	<div id="submit_services" class="checkout-services__submit">
		<button class="button"><?php echo esc_html__( 'Update', 'awebooking' ); ?></button>
	</div>
</form>
