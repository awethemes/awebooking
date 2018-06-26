<table class="panel" width="100%" cellpadding="0" cellspacing="0">
	<tr>
		<td class="panel-content">
			<table width="100%" cellpadding="0" cellspacing="0">
				<tr>
					<td class="panel-item">
						<?php echo wp_kses_post( wpautop( wptexturize( $slot ) ) ); ?>
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>
