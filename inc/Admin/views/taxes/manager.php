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
				<th><span><?php esc_html_e( 'Actions', 'awebooking' ); ?></span></th>
				<th style="text-align: right;">
					<a href="<?php echo esc_url( awebooking( 'url' )->admin_route( 'tax/create' ) ); ?>" class="button"><?php esc_html_e( 'New Tax or Fee', 'awebooking' ); ?></a>
				</th>
			</tr>
		</thead>

		<tbody>
			<?php if ( $taxes ) : ?>
			<?php foreach ( $taxes as $tax ) : ?>
				<tr>
					<td>
						<strong><?php echo esc_html( $tax->name ); ?></strong>
					</td>

					<td>
						<?php echo esc_html( $tax->code ); ?>
					</td>

					<td>
						<?php echo esc_html( $tax->type ); ?>
					</td>

					<td>
						<?php echo esc_html( $tax->category ); ?>
					</td>

					<td>
						<?php echo esc_html( $tax->amount_type ); ?>
					</td>

					<td>
						<?php echo esc_html( $tax->amount ); ?>
					</td>

					<td>
						<a href="<?php echo esc_url( awebooking( 'url' )->admin_route( "tax/{$tax->id}" ) ); ?>"><?php esc_html_e( 'Edit', 'awebooking' ); ?></a> |
						<a href="<?php echo esc_url( awebooking( 'url' )->admin_route( "tax/delete/{$tax->id}" ) ); ?>"><?php esc_html_e( 'Delete', 'awebooking' ); ?></a>
					</td>

					<td></td>
				</tr>

			<?php endforeach; ?>
		<?php else : ?>
			<tr>
				<td colspan="8">
					<p><?php esc_html_e( 'No items found.', 'awebooking' ); ?></p>
				</td>
			</tr>
		<?php endif; ?>
		</tbody>
	</table>

	<input type="submit" name="submit-cmb" class="button button-primary" value="<?php echo esc_html__( 'Save changes', 'awebooking' ); ?>">
</form>
