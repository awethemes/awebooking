<?php
/**
 * The template part for displaying a message that rooms cannot be found.
 *
 * This template can be overridden by copying it to {yourtheme}/awebooking/search/no-results.php.
 *
 * @see      http://docs.awethemes.com/awebooking/developers/theme-developers/
 * @author   awethemes
 * @package  AweBooking
 * @version  3.2.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<div class="search-rooms__empty vivre-card">
	<div class="vivre-card__icon">
		<img src="<?php echo esc_url( abrs_plugin_url( '/assets/img/no-rooms.svg' ) ); ?>" alt="<?php esc_attr_e( 'No rooms available', 'awebooking' ); ?>">
	</div>

	<div class="vivre-card__title">
		<?php esc_html_e( 'No rooms available', 'awebooking' ); ?>
	</div>

	<p class="vivre-card__text">
		<?php esc_html_e( 'Sorry, there\'s no available room that fits your current request.', 'awebooking' ); ?>
	</p>

	<?php if ( ! abrs_reservation()->is_empty() ) : ?>

		<a class="button" href="<?php echo esc_url( abrs_get_checkout_url() ); ?>">
			<span><?php esc_html_e( 'Go to Checkout', 'awebooking' ); ?></span>
		</a>

	<?php endif; ?>
</div><!-- /.search-rooms__empty -->
