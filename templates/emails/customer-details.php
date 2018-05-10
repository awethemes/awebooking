<?php
/**
 * Customer details.
 *
 * This template can be overridden by copying it to yourtheme/awebooking/emails/customer-details.php.
 *
 * HOWEVER, on occasion AweBooking will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see      http://docs.awethemes.com/awebooking/developers/theme-developers/
 * @author   awethemes
 * @package  AweBooking
 * @version  3.1.0
 */

use AweBooking\Component\Country\Formatter;

?>

<h2><?php esc_html_e( 'Customer details', 'awebooking' ); ?></h2>

<table class="table table--customer" width="100%" cellpadding="0" cellspacing="0" style="text-align: left;">
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
				$formatted = ( new Formatter() )->format([
					'address_1'  => $booking->get( 'customer_address' ),
					'address_2'  => $booking->get( 'customer_address_2' ),
					'city'       => $booking->get( 'customer_city' ),
					'state'      => $booking->get( 'customer_state' ),
					'postcode'   => $booking->get( 'customer_postal_code' ),
					'country'    => $booking->get( 'customer_country' ),
				]);

				echo wp_kses_post( $formatted );
				?>
			</td>
		</tr>

		<?php if ( $booking->get( 'customer_email' ) ) : ?>
			<tr>
				<th><?php esc_html_e( 'Email address', 'awebooking' ); ?></th>
				<td><a href="mailto:<?php echo esc_attr( $booking->get( 'customer_email' ) ) ?>"><?php echo esc_html( $booking->get( 'customer_email' ) ); ?></a></td>
			</tr>
		<?php endif; ?>

		<?php if ( $booking->get( 'customer_phone' ) ) : ?>
			<tr>
				<th><?php esc_html_e( 'Phone', 'awebooking' ); ?></th>
				<td><?php echo abrs_make_phone_clickable( $booking->get( 'customer_phone' ) ); ?></td>
			</tr>
		<?php endif; ?>

	</tbody>
</table>
