<?php

global $the_room_type;

?><div class="awebooking-panel abwrap" id="room-type-general" style="display: block;">

	<div class="abrow abrs-postbox-title">
		<div class="abcol"><h3><?php esc_html_e( 'Rooms', 'awebooking' ); ?></h3></div>
	</div>

	<div class="abrow">
		<div class="abcol-6">

			<ul class="abrs-sortable" id="js-sorting-rooms">
				<?php foreach ( $the_room_type->get_rooms() as $i => $room ) : ?>
					<li class="abrs-sortable__item">
						<div class="abrs-sortable__head">
							<span class="abrs-sortable__handle"></span>
							<span class="abrs-sortable__order"><?php echo esc_html( $i + 1 ); ?></span>
						</div>

						<div class="abrs-sortable__body">
							<strong class="screen-reader-text"><?php echo esc_html( $room['name'] ); ?></strong>
							<input type="text" name="_rooms[<?php echo esc_attr( $room->get_id() ); ?>][name]" value="<?php echo esc_attr( $room['name'] ); ?>" class="transparent">
						</div>

						<div class="abrs-sortable__actions hidden">
							<a href="<?php echo esc_url( wp_nonce_url( abrs_admin_route( "/room/{$room->get_id()}" ), 'delete_room_' . $room->get_id() ) ); ?>" data-method="abrs-delete" title="<?php printf( esc_html__( 'Delete: %s', 'awebooking' ), esc_html( $room['name'] ) ); ?>">
								<span class="screen-reader-text"><?php echo esc_html__( 'Delete', 'awebooking' ); ?></span>
								<span class="dashicons dashicons-no-alt"></span>
							</a>
						</div>
					</li>
				<?php endforeach ?>
			</ul>

			<div class="abrs-input-addon" style="width: 150px; display: none;">
				<select name="">
					<?php for ( $i = 1; $i <= abrs_maximum_scaffold_rooms(); $i++ ) : ?>
						<option value="<?php echo esc_attr( $i ); ?>"><?php echo esc_html( $i ); ?></option>
					<?php endfor; ?>
				</select>

				<button class="button abrs-dashicons-button" title="<?php echo esc_html__( 'Generate rooms', 'awebooking' ); ?>">
					<span class="screen-reader-text"><?php echo esc_html__( 'Generate rooms', 'awebooking' ); ?></span>
					<span class="dashicons dashicons-update"></span>
				</button>
			</div>

		</div>
	</div>

	<div class="abrow abrs-postbox-title">
		<div class="abcol"><h3><?php esc_html_e( 'Occupancy', 'awebooking' ); ?></h3></div>
	</div>

	<div class="abrow">
		<div class="abcol-3 abcol-sm-12">
			<?php $form->show_field( '_maximum_occupancy' ); ?>
		</div>

		<div class="abcol-3 abcol-sm-12">
			<?php $form->show_field( 'number_adults' ); ?>
		</div>

		<?php if ( abrs_children_bookable() ) : ?>
			<div class="abcol-3 abcol-sm-12">
				<?php $form->show_field( 'number_children' ); ?>
			</div>
		<?php endif ?>

		<?php if ( abrs_infants_bookable() ) : ?>
			<div class="abcol-3 abcol-sm-12" style="border-right: none;">
				<?php $form->show_field( 'number_infants' ); ?>
			</div>
		<?php endif ?>
	</div>

	<?php if ( abrs_infants_bookable() ) : ?>
		<div class="abrow">
			<div class="abcol">
				<?php $form->show_field( '_infants_in_calculations' ); ?>
			</div>
		</div>
	<?php endif ?>

	<div class="abrs-note abrs-mt1" style="max-width: 720px;">
		<h4><?php esc_html_e( 'Some notes on setting capacity', 'awebooking' ); ?></h4>
		<?php echo wp_kses_post( wpautop( __( "The number of adults, children etc. <b>do not</b> need to add up to the maximum occupancy. A room could sleep a maximum of 4 people, but the max adults may be 2 and max children 3. \n This would allow your guests to choose 2 adults and 2 children, or 1 adult and 3 children. (But never 2 adults and 3 children as this would exceed the max capacity.)", 'awebooking' ) ) ); ?>
	</div>
</div><!-- /#room-type-general -->
