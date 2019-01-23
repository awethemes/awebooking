<?php
/**
 * The template for displaying book button.
 *
 * This template can be overridden by copying it to {yourtheme}/awebooking/book-button.php.
 *
 * @see      http://docs.awethemes.com/awebooking/developers/theme-developers/
 * @author   awethemes
 * @package  AweBooking
 * @version  3.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

?>

<form method="POST" action="<?php echo esc_url( abrs_route( '/reservation/book-room' ) ); ?>">
	<?php wp_nonce_field( 'book-room', '_wpnonce', true ); ?>

	<?php if ( abrs_running_on_multilanguage() ) : ?>
		<input type="hidden" name="lang" value="<?php echo esc_attr( abrs_multilingual()->get_current_language() ); ?>">
	<?php endif ?>

	<?php if ( abrs_multiple_hotels() && $res_request->get_hotel() ) : ?>
		<input type="hidden" name="hotel" value="<?php echo esc_attr( $res_request->get_hotel() ); ?>">
	<?php endif ?>

	<input type="hidden" name="check_in" value="<?php echo esc_attr( $res_request->check_in ); ?>">
	<input type="hidden" name="check_out" value="<?php echo esc_attr( $res_request->check_out ); ?>">
	<input type="hidden" name="adults" value="<?php echo esc_attr( $res_request->adults ); ?>">

	<?php if ( abrs_children_bookable() && $res_request->children ) : ?>
		<input type="hidden" name="children" value="<?php echo esc_attr( $res_request->children ); ?>">
	<?php endif ?>

	<?php if ( abrs_infants_bookable() && $res_request->infants ) : ?>
		<input type="hidden" name="infants" value="<?php echo esc_attr( $res_request->infants ); ?>">
	<?php endif ?>

	<?php if ( $args['room_type'] > 0 ) : ?>
		<input type="hidden" name="room_type" value="<?php echo esc_attr( $args['room_type'] ); ?>">
	<?php endif ?>

	<?php if ( $args['show_button'] ) : ?>
		<button <?php echo abrs_html_attributes( $args['button_atts'] ); // WPCS: XSS OK. ?>><?php echo $args['button_text']; // WPCS: XSS OK. ?></button>
	<?php endif; ?>
</form>
