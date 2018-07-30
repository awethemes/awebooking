<?php
/**
 * Output the additionals form controls.
 *
 * This template can be overridden by copying it to {yourtheme}/awebooking/checkout/form-additionals.php
 *
 * @see      http://docs.awethemes.com/awebooking/developers/theme-developers/
 * @author   awethemes
 * @package  AweBooking
 * @version  3.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$additionals = $controls->get_section( 'additionals' );

if ( empty( $additionals->fields ) ) {
	return;
}

?>

<div id="guest-additional-information" class="checkout__section checkout__section--guest-additional-information">
	<header class="checkout__section-header">
		<h3 class="checkout__section__title"><?php esc_html_e( 'Additional information', 'awebooking' ); ?></h3>
	</header>

	<?php do_action( 'abrs_before_additional_information' ); ?>

	<div class="guest-additional-information-fields">
		<?php foreach ( $additionals->fields as $field_args ) : ?>

			<?php $controls->show_field( $field_args ); ?>

		<?php endforeach; ?>
	</div>

	<?php do_action( 'abrs_after_additional_information' ); ?>
</div>
