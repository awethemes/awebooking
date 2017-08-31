<?php

use AweBooking\Support\Carbonate;

echo '<ul class="booking_notes">';

if ( $notes ) {

	foreach ( $notes as $note ) {
		$note_classes   = array( 'note' );
		$note_classes[] = get_comment_meta( $note->comment_ID, 'is_customer_note', true ) ? 'customer-note' : '';
		$note_classes[] = ( __( 'AweBooking', 'awebooking' ) === $note->comment_author ) ? 'system-note' : '';
		$note_classes   = apply_filters( 'awebooking/booking_note_class', array_filter( $note_classes ), $note );

		$note_datetime = Carbonate::create_datetime( $note->comment_date );

		?><li rel="<?php echo absint( $note->comment_ID ); ?>" class="<?php echo esc_attr( implode( ' ', $note_classes ) ); ?>">
			<div class="note_content">
				<?php echo wpautop( wptexturize( wp_kses_post( $note->comment_content ) ) ); ?>
			</div>

			<p class="meta">
				<abbr class="exact-date" title="<?php echo $note->comment_date; ?>">
					<?php
					printf( esc_html__( 'added on %1$s at %2$s', 'awebooking' ),
						$note_datetime->date_i18n( awebooking( 'setting' )->get_date_format() ),
						$note_datetime->date_i18n( awebooking( 'setting' )->get_time_format() )
					); ?>
				</abbr>

				<?php
				if ( __( 'AweBooking', 'awebooking' ) !== $note->comment_author ) :
					/* translators: %s: note author */
					printf( ' ' . esc_html__( 'by %s', 'awebooking' ), $note->comment_author );
				endif;
				?>
				<a href="#" class="delete_note" role="button"><?php esc_html_e( 'Delete note', 'awebooking' ); ?></a>
			</p>
		</li>
		<?php
	}
} else {
	echo '<li>' . esc_html__( 'There are no notes yet.', 'awebooking' ) . '</li>';
}

echo '</ul>';
?>

<div class="add_note">
	<p>
		<label for="add_booking_note"><?php esc_html_e( 'Add note', 'awebooking' ); ?></label>
		<textarea type="text" name="order_note" id="add_booking_note" class="input-text" cols="20" rows="5"></textarea>
	</p>

	<p>
		<label>
			<input type="checkbox" name="" checked="" disabled="">
			<?php echo esc_html__( 'Private note', 'awebooking' ) ?>
		</label>

		<button type="button" class="add_note button" style="float: right;"><?php esc_html_e( 'Add note', 'awebooking' ); ?></button>
	</p>

	<div class="clear"></div>
</div>
