<div class="form-booking-note">
	<div style="margin-bottom: 5px;">
		<label for="js-booking-note" class="screen-reader-text"><strong><?php esc_html_e( 'Leave note:', 'awebooking' ); ?></strong></label>
		<textarea name="booking_note" id="js-booking-note" class="input-text" rows="2" autocomplete="off" style="width: 100%;" placeholder="<?php esc_html_e( 'Write some note', 'awebooking' ); ?>"></textarea>
	</div>

	<div class="abrs-mb0">
		<label style="line-height: 28px;"><input type="checkbox" id="js-customer-note"> <?php echo esc_html__( 'Customer note?', 'awebooking' ); ?></label>
		<button type="button" class="button abrs-button abrs-fright" id="js-add-note"><?php esc_html_e( 'Add note', 'awebooking' ); ?></button>
	</div>

	<div class="clear"></div>
</div>

<ul class="booking-notes" id="js-booking-notes">
	<?php if ( abrs_blank( $notes ) ) : ?>

		<li class="abrs-pb1"><p class="awebooking-no-items"><?php esc_html_e( 'There are no notes yet.', 'awebooking' ); ?></p></li>

	<?php else : ?>

		<?php foreach ( $notes as $note ) : ?>
			<?php include trailingslashit( __DIR__ ) . 'html-booking-note.php'; ?>
		<?php endforeach ?>

	<?php endif ?>
</ul><!-- /.booking-notes -->
