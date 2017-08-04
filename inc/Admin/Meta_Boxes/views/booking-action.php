<div id="minor-publishing">
	<div id="actions" style="padding: 10px;">
		<select name="awebooking_action">
			<option value=""><?php esc_html_e( 'Actions', 'awebooking' ); ?></option>
			<option value="send_email_cancelled_order">Resend Cancelled order</option>
			<option value="send_email_customer_processing_order">Resend Processing order</option>
			<option value="send_email_customer_completed_order">Resend Completed order</option>
			<option value="send_email_customer_invoice">Resend Customer invoice</option>
		</select>

		<button class="button wc-reload"><span><?php esc_html_e( 'Apply', 'awebooking' ); ?></span></button>
	</div>
</div>

<div id="major-publishing-actions">
	<div id="delete-action">
		<?php if ( current_user_can( 'delete_post', $post->ID ) ) :

			if ( ! EMPTY_TRASH_DAYS ) {
				$delete_text = esc_html__( 'Delete permanently', 'awebooking' );
			} else {
				$delete_text = esc_html__( 'Move to trash', 'awebooking' );
			}

			?><a class="submitdelete deletion" href="<?php echo esc_url( get_delete_post_link( $post->ID ) ); ?>"><?php print $delete_text; ?></a><?php
		endif; ?>
	</div>

	<div id="publishing-action">
		<span class="spinner"></span>
		<input type="submit" class="button button-primary" name="save" value="<?php echo 'auto-draft' === $post->post_status ? esc_attr__( 'Create', 'awebooking' ) : esc_attr__( 'Update', 'awebooking' ); ?>" />
	</div>

	<div class="clear"></div>
</div>
