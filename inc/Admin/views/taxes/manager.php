<?php
use AweBooking\Admin\Forms\New_Tax_Form;
use AweBooking\Model\Tax;

$taxes = Tax::query();

?><form class="cmb-form" method="POST" action="" style="margin-top: 1em;">
	<?php wp_nonce_field( 'awebooking_reservation_tax', '_wpnonce', true ); ?>

	<table class="wp-list-table widefat fixed striped">
		<thead>
			<tr>
				<th style="width: 20%;"><span><?php esc_html_e( 'Name', 'awebooking' ); ?></span></th>
				<th><span><?php esc_html_e( 'Code', 'awebooking' ); ?></span></th>
				<th style="width: 65px;"><span><?php esc_html_e( 'Type', 'awebooking' ); ?></span></th>
				<th><span><?php esc_html_e( 'Category', 'awebooking' ); ?></span></th>
				<th><span><?php esc_html_e( 'Amount type', 'awebooking' ); ?></span></th>
				<th><span><?php esc_html_e( 'Amount', 'awebooking' ); ?></span></th>
				<th style="text-align: right;">
					<a href="<?php echo esc_url( awebooking( 'url' )->admin_route( 'tax/create' ) ); ?>" class="button"><?php esc_html_e( 'New Tax or Fee', 'awebooking' ); ?></a>
				</th>
			</tr>
		</thead>

		<tbody>
			<?php if ( $taxes->isNotEmpty() ) : ?>
				<?php foreach ( $taxes as $tax ) : ?>
					<tr>
						<td>
							<strong><?php echo esc_html( $tax->get_name() ); ?></strong>
							<div class="row-actions afloat-right">
								<span class="edit"><a href="<?php echo esc_url( $tax->get_edit_url() ); ?>"><?php esc_html_e( 'Edit', 'awebooking' ); ?></a> | </span>
								<span class="trash"><a href="<?php echo esc_url( $tax->get_delete_url() ); ?>" data-method="awebooking-delete" class="submitdelete"><?php esc_html_e( 'Delete', 'awebooking' ); ?></a></span>
							</div>
						</td>

						<td>
							<?php echo esc_html( $tax->get_code() ); ?>
						</td>

						<td>
							<?php echo esc_html( $tax->get_type_label() ); ?>
						</td>

						<td>
							<?php echo esc_html( $tax->get_category_label() ); ?>
						</td>

						<td>
							<?php echo esc_html( $tax->get_amount_type_label() ); ?>
						</td>

						<td>
							<?php echo esc_html( $tax->get_amount_label() ); ?>
						</td>

						<td></td>
					</tr>

				<?php endforeach; ?>
			<?php else : ?>
				<tr>
					<td colspan="7">
						<p><?php esc_html_e( 'No items found.', 'awebooking' ); ?></p>
					</td>
				</tr>
			<?php endif; ?>
		</tbody>
	</table>
</form>
