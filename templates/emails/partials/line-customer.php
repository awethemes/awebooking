<?php
/**
 * Display the customer details in a booking.
 *
 * This template can be overridden by copying it to {yourtheme}/awebooking/emails/partials/line-customer.php.
 *
 * @see      http://docs.awethemes.com/awebooking/developers/theme-developers/
 * @author   awethemes
 * @package  AweBooking
 * @version  3.1.0
 */

/* @var \AweBooking\Model\Booking $booking */

?><table class="table-customer" width="100%" cellpadding="0" cellspacing="0">
	<tbody>
		<tr>
			<th><?php esc_html_e( 'Name', 'awebooking' ); ?></th>
			<td><?php echo esc_html( $booking->get_customer_fullname() ); ?></td>
		</tr>

		<?php if ( $customer_company = $booking->get( 'customer_company' ) ) : ?>
			<tr>
				<th><?php esc_html_e( 'Company', 'awebooking' ); ?></th>
				<td><?php echo esc_html( $customer_company ); ?></td>
			</tr>
		<?php endif; ?>

		<tr>
			<th><?php esc_html_e( 'Address', 'awebooking' ); ?></th>
			<td>
				<?php
				print abrs_format_address([ // WPCS: XSS OK.
					'address_1' => $booking->get( 'customer_address' ),
					'address_2' => $booking->get( 'customer_address_2' ),
					'city'      => $booking->get( 'customer_city' ),
					'state'     => $booking->get( 'customer_state' ),
					'postcode'  => $booking->get( 'customer_postal_code' ),
					'country'   => $booking->get( 'customer_country' ),
				]);
				?>
			</td>
		</tr>

		<?php if ( $customer_email = $booking->get( 'customer_email' ) ) : ?>
			<tr>
				<th><?php esc_html_e( 'Email', 'awebooking' ); ?></th>
				<td><?php echo make_clickable( $customer_email ); // WPCS: XSS OK. ?></td>
			</tr>
		<?php endif; ?>

		<?php if ( $customer_phone = $booking->get( 'customer_phone' ) ) : ?>
			<tr>
				<th><?php esc_html_e( 'Phone', 'awebooking' ); ?></th>
				<td><?php echo abrs_make_phone_clickable( $customer_phone ); // WPCS: XSS OK. ?></td>
			</tr>
		<?php endif; ?>
	</tbody>
</table>
