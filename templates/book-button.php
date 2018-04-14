<form method="POST" action="<?php echo esc_url( abrs_route( '/book-room' ) ); ?>">
	<input type="hidden" name="_nonce" value="<?php echo esc_attr( wp_create_nonce( 'book-room' ) ); ?>">

	<input type="hidden" name="check_in" value="<?php echo esc_attr( $request->check_in ); ?>">
	<input type="hidden" name="check_out" value="<?php echo esc_attr( $request->check_out ); ?>">
	<input type="hidden" name="adults" value="<?php echo esc_attr( $request->adults ); ?>">

	<?php if ( abrs_children_bookable() && $request->children ) : ?>
		<input type="hidden" name="adults" value="<?php echo esc_attr( $request->children ); ?>">
	<?php endif ?>

	<?php if ( abrs_infants_bookable() && $request->infants ) : ?>
		<input type="hidden" name="adults" value="<?php echo esc_attr( $request->infants ); ?>">
	<?php endif ?>

	<?php if ( $args['room'] > 0 ) : ?>
		<input type="hidden" name="book_room" value="<?php echo esc_attr( $args['room'] ); ?>">
	<?php elseif ( $args['room_type'] > 0 ) : ?>
		<input type="hidden" name="room_type" value="<?php echo esc_attr( $args['room_type'] ); ?>">
	<?php endif ?>

	<button <?php echo abrs_html_attributes( $args['button_atts'] ); // WPCS: XSS OK. ?>><?php echo $args['button_text']; // WPCS: XSS OK. ?></button>
</form>
