<?php
/**
 * The Template for displaying check form.
 *
 * This template can be overridden by copying it to yourtheme/awebooking/check-availability-form.php.
 *
 * @author 		awethemes
 * @package 	AweBooking/Templates
 * @version     1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$page_id = intval( abkng_config( 'page_check_availability' ) );
$link = get_the_permalink( $page_id );
?>
<form action="<?php echo esc_url( $link ); ?>" class="awebooking-check-form">
	<?php if ( ! get_option( 'permalink_structure' ) ) : ?>
		<input type="hidden" name="p" value="<?php echo esc_attr( $page_id ) ?>">
	<?php endif ?>

	<?php if ( awebooking()->is_multi_language() ) : ?>
		<input type="hidden" name="lang" value="<?php echo esc_attr( awebooking( 'multilingual' )->get_active_language() ) ?>">
	<?php endif ?>

	<div class="awebooking-check-form__wrapper">
		<h2 class="awebooking-heading"><?php esc_html_e( 'Your Reservation', 'awebooking' ); ?></h2>
		<div class="awebooking-check-form__content">

			<?php abkng_template_check_form_input_time(); ?>

			<?php
			if ( ! $atts['hide_location'] ) {
				abkng_template_check_form_input_location();
			}
			?>

			<?php abkng_template_check_form_input_capacity(); ?>

			<div class="awebooking-field mb-0">
				<div class="awebooking-field-group">
					<button type="submit" class="awebooking-btn"><?php esc_html_e( 'CHECK AVAILABILITY', 'awebooking' ); ?></button>
				</div>
			</div>
		</div>
	</div>
	<?php //wp_nonce_field( 'check_availability' . get_the_ID() ); ?>
</form>
