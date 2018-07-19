<?php

/* @var $the_booking \AweBooking\Model\Booking */
global $the_booking;

$service_items = $the_booking->get_services();

?>

<table class="awebooking-table abrs-booking-rooms widefat fixed">
	<thead>
		<tr>
			<th style="width: 80%;"><?php echo esc_html__( 'Service', 'awebooking' ); ?></th>
			<th class="abrs-text-right"><span><?php esc_html_e( 'Price', 'awebooking' ); ?></span></th>
		</tr>
	</thead>

	<tbody>
		<?php if ( abrs_blank( $service_items ) ) : ?>

			<tr>
				<td colspan="2">
					<p class="awebooking-no-items"><?php esc_html_e( 'No services', 'awebooking' ); ?></p>
				</td>
			</tr>

		<?php else : ?>

			<?php foreach ( $service_items as $service_item ) : ?>
				<?php
				/* @var $service_item \AweBooking\Model\Booking\Service_Item */
				$service = abrs_get_service( $service_item->get( 'service_id' ) );

				$action_link = abrs_admin_route( "/booking-service/{$service_item->get_id()}" );
				?>

				<tr>
					<td>
						<?php
						$thumbnail = '<span class="abrs-no-image"></span>';

						if ( $service && has_post_thumbnail( $service->get_id() ) ) {
							$thumbnail = get_the_post_thumbnail( $service->get_id(), 'thumbnail' );
						}

						printf( '<div class="abrs-thumb-image abrs-fleft" style="margin-right: 10px;">%2$s</div>', esc_url( '#' ), $thumbnail ); // @wpcs: XSS OK.
						?>

						<strong class="row-title"><?php echo esc_html( $service_item->get_name() ); ?></strong>
					</td>

					<td class="abrs-text-right">

						<?php if ( $the_booking->is_editable() ) : ?>

							<div class="row-actions abrs-fleft">
								<span class="trash"><a href="<?php echo esc_url( wp_nonce_url( $action_link, "delete_service_{$service_item->get_id()}" ) ); ?>" data-method="abrs-delete"><?php esc_html_e( 'Delete', 'awebooking' ); ?></a></span>
							</div>

						<?php endif; ?>

						<p>
							<?php
							printf( /* translators: %1$s quantity, %2$s unit price */
								esc_html_x( '%1$s x %2$s', 'admin booking service price', 'awebooking' ),
								absint( $service_item->get( 'quantity' ) ),
								abrs_format_price( $service_item->get( 'price' ) )
							); // WPCS: xss ok.
							?>
						</p>

						<?php abrs_price( $service_item->get( 'total' ), $the_booking->get( 'currency' ) ); ?>
					</td>
				</tr>

			<?php endforeach; ?>
		<?php endif; ?>

	</tbody>
</table><!-- /.awebooking-table -->
