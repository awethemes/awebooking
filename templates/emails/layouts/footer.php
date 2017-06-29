<tr>
	<td>
		<table class="footer" align="center" width="570" cellpadding="0" cellspacing="0">
			<tr>
				<td class="content-cell" align="center">
					<?php $copyright = sprintf( esc_html__( '&copy; %1$s %2$s. All rights reserved.', 'awebooking' ), date( 'Y' ), get_bloginfo( 'name' ) );
					echo esc_html( apply_filters( 'awebooking/email_footer', $copyright ) ); ?>
				</td>
			</tr>
		</table>
	</td>
</tr>
