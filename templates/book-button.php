<form method="POST" action="<?php echo esc_url( abrs_route( '/book-room' ) ); ?>">
	<?php wp_nonce_field( 'book-room', '_wpnonce', true ); ?>

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
