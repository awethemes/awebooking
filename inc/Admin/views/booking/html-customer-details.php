<table class="awebooking-table awebooking-table--bordered abrs-text-left" width="100%" cellpadding="0" cellspacing="0">
	<tbody>
		<tr>
			<th><?php esc_html_e( 'Name', 'awebooking' ); ?></th>
			<td><?php echo esc_html( $booking->get_customer_fullname() ); ?></td>
		</tr>

		<?php if ( $booking->get( 'customer_company' ) ) : ?>
			<tr>
				<th><?php esc_html_e( 'Company', 'awebooking' ); ?></th>
				<td><?php echo esc_html( $booking->get( 'customer_company' ) ); ?></td>
			</tr>
		<?php endif; ?>

		<tr>
			<th><?php esc_html_e( 'Address', 'awebooking' ); ?></th>
			<td>
				<?php
				echo wp_kses_post( abrs_format_address([
					'address_1'  => $booking->get( 'customer_address' ),
					'address_2'  => $booking->get( 'customer_address_2' ),
					'city'       => $booking->get( 'customer_city' ),
					'state'      => $booking->get( 'customer_state' ),
					'postcode'   => $booking->get( 'customer_postal_code' ),
					'country'    => $booking->get( 'customer_country' ),
				]));
				?>
			</td>
		</tr>

		<?php if ( $booking->get( 'customer_email' ) ) : ?>
			<tr>
				<th><?php esc_html_e( 'Email', 'awebooking' ); ?></th>
				<td><?php echo wp_kses_post( make_clickable( $booking->get( 'customer_email' ) ) ); ?></td>
			</tr>
		<?php endif; ?>

		<?php if ( $booking->get( 'customer_phone' ) ) : ?>
			<tr>
				<th><?php esc_html_e( 'Phone', 'awebooking' ); ?></th>
				<td><?php echo abrs_make_phone_clickable( $booking->get( 'customer_phone' ) ); // WPCS: XSS OK. ?></td>
			</tr>
		<?php endif; ?>

	</tbody>
</table>
