<?php
/* @vars $request, $booking, $controls */

?><div class="wrap" style="max-width: 1200px;">
	<h1 class="wp-heading-inline"><?php esc_html_e( 'Add Room', 'awebooking' ); ?></h1>
	<span><?php esc_html_e( 'Reference', 'awebooking' ); ?> <a href="<?php echo esc_url( get_edit_post_link( $booking->get_id() ) ); ?>">#<?php echo esc_html( $booking->get_booking_number() ); ?></a></span>
	<hr class="wp-header-end">

	<form method="GET" action="<?php echo esc_url( abrs_admin_route( '/booking-room' ) ); ?>">
		<input type="hidden" name="awebooking" value="<?php echo esc_attr( $request->route_path() ); ?>">
		<input type="hidden" name="refer" value="<?php echo esc_attr( $booking->get_id() ); ?>">

		<div class="abrs-toolbar abrs-search-toolbar cmb2-inline-metabox">
			<div class="abrow abrs-ptb1">
				<div class="abcol-3 abcol-sm-8">
					<?php $controls['date']->display(); ?>
				</div>

				<div class="abcol-1 abcol-sm-4 abrs-pl0">
					<button class="button abrs-button" type="submit"><span class="dashicons dashicons-search"></span><?php esc_html_e( 'Search', 'awebooking' ); ?></button>
				</div>
			</div>
		</div><!-- /.abrs-search-toolbar -->
	</form>

	<form method="POST" action="<?php echo esc_url( abrs_admin_route( "booking/{$booking->get_id()}/room" ) ); ?>">
		<?php wp_nonce_field( 'add_booking_room', '_wpnonce' ); ?>

		<?php if ( isset( $results ) ) : ?>
			<table class="widefat fixed">
				<thead>
					<tr>
						<th>#</th>
						<th></th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ( $results as $avai ) : ?>
						<?php $this->partial( 'booking/html-avai-row.php', compact( 'avai' ) ); ?>
					<?php endforeach ?>
				</tbody>
			</table>
		<?php endif ?>

	</form>
</div><!-- /.wrap -->
