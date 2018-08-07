<?php
/**
 * This template displaying the services details.
 *
 * This template can be overridden by copying it to {yourtheme}/awebooking/reservation/services.php.
 *
 * @see      http://docs.awethemes.com/awebooking/developers/theme-developers/
 * @author   awethemes
 * @package  AweBooking
 * @version  3.1.0
 */

$services = abrs_reservation()->get_services();

if ( abrs_blank( $services ) ) {
	return;
}

?>

<div class="reservation__section reservation__section--service">
	<?php foreach ( $services as $item ) : ?>
		<div class="servicedetails-service">
			<dl class="servicedetails-service__list">
				<dt><?php echo esc_html( $item->get_name() ); ?></dt>
				<dd>
					<span class="screen-reader-text"><?php esc_html_e( 'Price', 'awebooking' ); ?></span>
					<?php
					/* translators: %1$s quantity, %2$s service price */
					printf( esc_html_x( '%1$s x %2$s', 'quantity x price service', 'awebooking' ),
						absint( $item->get_quantity() ),
						abrs_format_price( $item->get_price() )
					); // WPCS: xss ok.
					?>
				</dd>

				<dt>&nbsp;</dt>
				<dd><?php abrs_price( $item->get_subtotal() ); ?></dd>
			</dl>

		</div><!-- /.servicedetails-service -->
	<?php endforeach; ?>
</div>
