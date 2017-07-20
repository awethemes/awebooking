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

$link = get_the_permalink( intval( awebooking_option( 'page_check_availability' ) ) );
?>
<form action="<?php echo esc_url( $link ); ?>" class="awebooking-check-form" method="GET">
	<input type="hidden" name="page_id" value="<?php echo intval( awebooking_option( 'page_check_availability' ) ); ?>">

	<div class="awebooking-check-form__wrapper">
		<h2 class="awebooking-heading"><?php esc_html_e( 'Your Reservation', 'awebooking' ); ?></h2>
		<div class="awebooking-check-form__content">

			<?php awebooking_template_check_form_input_time(); ?>

			<?php
			if ( ! $atts['hide_location'] ) {
				awebooking_template_check_form_input_location();
			}
			?>

			<?php awebooking_template_check_form_input_capacity(); ?>

			<div class="awebooking-field mb-0">
				<div class="awebooking-field-group">
					<button type="submit" class="awebooking-btn"><?php esc_html_e( 'CHECK AVAILABILITY', 'awebooking' ); ?></button>
				</div>
			</div>
		</div>
	</div>
	<?php //wp_nonce_field( 'check_availability' . get_the_ID() ); ?>
</form>
