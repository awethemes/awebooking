<?php

$note_classes   = [ 'booking-note' ];
$note_classes[] = $note->customer_note ? 'customer-note' : '';
$note_classes[] = 'system' === $note->added_by ? 'system-note' : '';
$note_classes   = apply_filters( 'abrs_booking_note_class', array_filter( $note_classes ), $note );

?><li rel="<?php echo absint( $note->id ); ?>" class="<?php echo esc_attr( implode( ' ', $note_classes ) ); ?>">
	<button type="button" class="button abrs-delete-button js-delete-note" role="button" title="<?php esc_html_e( 'Delete Note', 'awebooking' ); ?>">
		<span class="screen-reader-text"><?php esc_html_e( 'Delete Note', 'awebooking' ); ?></span>
		<span class="dashicons dashicons-no-alt"></span>
	</button>

	<div class="booking-note__content">
		<?php echo wp_kses_post( wpautop( wptexturize( make_clickable( $note->content ) ) ) ); ?>
	</div>

	<div class="booking-note__meta">
		<abbr class="exact-date" title="<?php echo esc_attr( $note->date_created->toDateTimeString() ); ?>">
			<?php
			$date_created = $note->date_created;

			// Check if the booking was created within the last 24 hours, and not in the future.
			// We will show the date as human readable date time by using human_time_diff.
			if ( ! $date_created->isFuture() && $date_created->gt( abrs_date( 'now' )->subDay() ) ) {
				/* translators: %s: human-readable time difference */
				printf( esc_html( _x( '%s ago', '%s = human-readable time difference', 'awebooking' ) ),
					esc_html( human_time_diff( $date_created->getTimestamp(), current_time( 'timestamp', true ) ) )
				);
			} else {
				/* translators: 1: Date string, 2: Time string. */
				printf( esc_html__( 'added on %1$s at %2$s', 'awebooking' ),
					esc_html( $note->date_created->date_i18n( abrs_get_date_format() ) ),
					esc_html( $note->date_created->date_i18n( abrs_get_time_format() ) )
				);
			}
			?>
		</abbr>

		<?php
		if ( 'system' !== $note->added_by ) {
			/* translators: %s: note author */
			printf( ' ' . esc_html__( 'by %s', 'awebooking' ), esc_html( $note->added_by ) );
		}
		?>

	</div>
</li>
