<?php

use AweBooking\Model\Room_Type;
use AweBooking\Source\Store;
use AweBooking\Admin\Forms\New_Source_Form;

$store = awebooking()->make( Store::class );

$primary_sources = (array) awebooking()->make( 'primary_sources' );

?><form class="cmb-form" method="POST" action="<?php echo esc_url( awebooking( 'url' )->admin_route( 'sources/bulk-update' ) ); ?>" style="margin-top: 1em;">
	<?php wp_nonce_field( 'awebooking_reservation_source', '_wpnonce', true ); ?>

	<table class="wp-list-table widefat fixed striped">
		<thead>
			<tr>
				<th style="width: 65px;"></th>
				<th style="width: 20%;"><span><?php esc_html_e( 'Sources', 'awebooking' ); ?></span></th>
				<th><span><?php esc_html_e( 'Taxes / Fees', 'awebooking' ); ?></span></th>
			</tr>
		</thead>

		<tbody>
			<?php foreach ( $primary_sources as $_source ) :
				$source = $store->get( $_source['uid'] );

				if ( is_null( $source ) ) {
					$source = array_merge( $_source, [
						'enabled' => true,
					]);
				}

				$this->partial( 'sources/direct_source_item.php', compact( 'source', 'store' ) );
			endforeach; ?>

			<?php foreach ( $store->all() as $source ) :
				$this->partial( 'sources/direct_source_item.php', compact( 'source', 'store' ) );
			endforeach; ?>
		</tbody>
	</table>

	<input type="submit" name="submit-cmb" class="button button-primary" value="<?php echo esc_html__( 'Save changes', 'awebooking' ); ?>">
</form>
