<tr>
	<td class="header">
		<table align="center" width="570" cellpadding="0" cellspacing="0">
			<tr>
				<td align="left">
					<a href="<?php echo esc_url( apply_filters( 'awebooking/email_header_url', get_site_url() ) ); ?>">
						<?php if ( $img = apply_filters( 'awebooking/email_header_image', abrs_get_option( 'email_header_image' ) ) ) : ?>
							<img src="<?php echo esc_url( $img ); ?>" alt="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" />
						<?php else : ?>
							<?php echo esc_html( apply_filters( 'awebooking/email_header_text', get_bloginfo( 'name', 'display' ) ) ); ?>
						<?php endif ?>
					</a>
				</td>
			</tr>
		</table>
	</td>
</tr>
