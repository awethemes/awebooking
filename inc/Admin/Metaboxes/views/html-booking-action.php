<div class="submitbox" id="awebooking-actions">
	<div id="minor-publishing">
		<div class="misc-pub-section abrs-input-addon">
			<select name="awebooking_action" style="width: 100%;">
				<option value=""><?php esc_html_e( 'Choose an action...', 'awebooking' ); ?></option>

				<?php foreach ( $booking_actions as $action => $title ) { ?>
					<option value="<?php echo esc_attr( $action ); ?>"><?php echo esc_html( $title ); ?></option>
				<?php } ?>
			</select>

			<button class="button abrs-dashicons-button">
				<span class="dashicons dashicons-arrow-right-alt2"></span>
				<span class="screen-reader-text"><?php esc_html_e( 'Apply', 'awebooking' ); ?></span>
			</button>
		</div>
	</div>

	<?php if ( isset( $checkout_scheduled ) && $checkout_scheduled ) : ?>
		<div class="abrs-plr1 abrs-label abrs-label--warning squared">
			<p>
				<?php
				printf( esc_html__( 'Auto update checkout status at %1$s (%2$s)', 'awebooking' ),
					esc_html( abrs_date_time( $checkout_scheduled )->toDateTimeString() ),
					esc_html( abrs_date_time( $checkout_scheduled )->diffForHumans( null, true, false, 2 ) )
				);
				?>
			</p>
		</div>
	<?php endif; ?>

	<div id="major-publishing-actions">
		<div id="delete-action">
			<?php
			if ( current_user_can( 'delete_post', $post->ID ) ) {
				if ( ! EMPTY_TRASH_DAYS ) {
					$delete_text = esc_html__( 'Delete permanently', 'awebooking' );
				} else {
					$delete_text = esc_html__( 'Move to trash', 'awebooking' );
				}

				print '<a class="submitdelete deletion" href="' . esc_url( get_delete_post_link( $post->ID ) ) . '">' . esc_html( $delete_text ) . '</a>';
			}
			?>
		</div>

		<div id="publishing-action">
			<span class="spinner"></span>
			<input type="submit" class="button button-primary" name="save" value="<?php echo 'auto-draft' === $post->post_status ? esc_attr__( 'Create', 'awebooking' ) : esc_attr__( 'Update', 'awebooking' ); ?>" />
		</div>

		<div class="clear"></div>
	</div>
</div>
