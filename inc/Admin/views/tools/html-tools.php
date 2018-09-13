<form method="POST" action="<?php echo esc_url( abrs_admin_route( '/tools/execute' ) ); ?>">
	<?php wp_nonce_field( 'awebooking_execute_task' ); ?>

	<table class="widefat fixed striped" cellspacing="0">
		<tbody>
		<?php foreach ( $tools as $action => $tool ) : ?>
			<tr id="tool_<?php echo sanitize_html_class( $action ); ?>">
				<th style="width: 80%;">
					<strong><?php echo esc_html( $tool['name'] ); ?></strong>
					<p><?php echo wp_kses_post( $tool['desc'] ); ?></p>
				</th>

				<td style="width: 20%; text-align: right;">
					<button name="task" value="<?php echo esc_attr( $action ); ?>" class="button <?php echo sanitize_html_class( $action ); ?>"><?php echo esc_html( $tool['button'] ); ?></button>
				</td>
			</tr>
		<?php endforeach; ?>
		</tbody>
	</table>
</form>
