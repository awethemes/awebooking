<?php

/* @var $the_booking \AweBooking\Model\Booking */
global $the_booking;

$service_items = $the_booking->get_services();

?>

<table class="awebooking-table abrs-booking-rooms widefat fixed">
	<thead>
		<tr>
			<th><?php esc_html_e( 'Service', 'awebooking' ); ?></th>

			<th class="abrs-text-right price-area" style="width: 80px;">
				<span><?php esc_html_e( 'Unit Price', 'awebooking' ); ?></span>
			</th>

			<th class="abrs-text-right price-area" style="width: 40px;">
				<span><?php esc_html_e('Qty', 'awebooking') ?></span>
			</th>

			<th class="abrs-text-right price-area" style="width: 80px;">
				<span><?php esc_html_e( 'Subtotal', 'awebooking' ); ?></span>
			</th>

			<th class="abrs-text-right price-area" style="width: 60px;">
				<span><?php esc_html_e( 'TAX', 'awebooking' ); ?></span>
			</th>

			<th class="abrs-text-right price-area" style="width: 80px;">
				<span><?php esc_html_e( 'Total', 'awebooking' ); ?></span>
			</th>
		</tr>
	</thead>

	<tbody>
		<?php if ( abrs_blank( $service_items ) ) : ?>

			<tr>
				<td colspan="6">
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

						<?php if ( $the_booking->is_editable() ) : ?>

							<div class="row-actions">
								<span class="trash"><a href="<?php echo esc_url( wp_nonce_url( $action_link, "delete_service_{$service_item->get_id()}" ) ); ?>" data-method="abrs-delete"><?php esc_html_e( 'Delete', 'awebooking' ); ?></a></span>
							</div>

						<?php endif; ?>
					</td>

					<td class="abrs-text-right price-area">
						<?php abrs_price( $service_item->get( 'price' ), $the_booking->get( 'currency' ) ); ?>
					</td>

					<td class="abrs-text-right price-area">
						<span class="abrs-badge"><?php echo absint($service_item->get('quantity')); ?></span>
					</td>

					<td class="abrs-text-right price-area">
						<?php abrs_price( $service_item->get( 'subtotal' ), $the_booking->get( 'currency' ) ); ?>
					</td>

					<td class="abrs-text-right price-area">
						<?php abrs_price( $service_item->get( 'total_tax' ), $the_booking->get( 'currency' ) ); ?>
					</td>

					<td class="abrs-text-right price-area">
						<strong><?php abrs_price( $service_item->get( 'total' ), $the_booking->get( 'currency' ) ); ?></strong>
					</td>
				</tr>

			<?php endforeach; ?>
		<?php endif; ?>

	</tbody>
</table><!-- /.awebooking-table -->
