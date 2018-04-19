<?php

list( $res_request, $room_type, $rooms, $plans ) = array_values( $avai );
list( $rate_plan, $rates, $pricing )  = array_values( $plans->first() );

// Remain rooms.
$remain_rooms = $rooms->remains();

// Leave if empty remains rates, that mean we have no price.
if ( 0 === count( $rates->remains() ) ) {
	return;
}

// Build the select occupancy options.
$occupancy_options = '';
for ( $i = 1; $i <= $room_type['maximum_occupancy']; $i++ ) {
	$occupancy_options .= '<option value="' . esc_attr( $i ) . '">' . esc_html( $i ) . '</option>';
}

// Build the input prefix.
$input_prefix = 'reservation[' . $room_type->get_id() . ']';

?><tr>
	<td>
		<?php
		$thumbnail = '<span class="abrs-no-image"></span>';
		if ( has_post_thumbnail( $room_type->get_id() ) ) {
			$thumbnail = get_the_post_thumbnail( $room_type->get_id(), 'thumbnail' );
		}

		printf( '<a href="%1$s" target="_blank" class="abrs-thumb-image">%2$s</a>', esc_url( get_edit_post_link( $room_type->get_id() ) ), $thumbnail ); // @wpcs: XSS OK.
		?>
	</td>

	<td>
		<a class="row-title" href="<?php echo esc_url( get_edit_post_link( $room_type->get_id() ) ); ?>" target="_blank"><?php echo esc_html( $room_type->get_title() ); ?></a>

		<div class="">
			<span class="tippy" data-tippy-html="#js-debug-room-<?php echo esc_attr( $room_type->get_id() ); ?>" data-tippy-delay="[100, 100000]" data-tippy-theme="abrs-tippy" data-tippy-interactive="true" data-tippy-arrow="true">
				<span class="dashicons dashicons-info"></span>
			</span>

			<?php
			/* translators: %d Number of rooms left */
			echo esc_html( sprintf( _n( '%d room left', '%d rooms left', count( $remain_rooms ), 'awebooking' ), count( $remain_rooms ) ) );
			?>
		</div>

		<div id="js-debug-room-<?php echo esc_attr( $room_type->get_id() ); ?>" style="display: none;">
			<?php abrs_admin_template_part( 'booking/html-debug-rooms.php', compact( 'rooms' ) ); ?>
		</div>
	</td>

	<td>
		<select name="<?php echo esc_attr( $input_prefix . '[room]' ); ?>">
			<?php foreach ( $remain_rooms as $room_info ) : ?>
			<option value="<?php echo esc_html( abrs_optional( $room_info['item'] )->get_id() ); ?>"><?php echo esc_html( abrs_optional( $room_info['item'] )->get( 'name' ) ); ?></option>
			<?php endforeach ?>
		</select>
	</td>

	<td>
		<div class="select-occupancy">
			<p>
				<label class="screen-reader-text"><?php esc_html_e( 'Adults', 'awebooking' ); ?></label>
				<select name="<?php echo esc_attr( $input_prefix . '[adults]' ); ?>" title="<?php esc_html_e( 'Select Adults', 'awebooking' ); ?>">
					<option value="1"><?php esc_html_e( 'Adults', 'awebooking' ); ?></option>
					<?php print $occupancy_options; // WPCS: XSS OK. ?>
				</select>
			</p>

			<?php if ( abrs_children_bookable() ) : ?>
				<p>
					<label class="screen-reader-text"><?php esc_html_e( 'Children', 'awebooking' ); ?></label>
					<select name="<?php echo esc_attr( $input_prefix . '[children]' ); ?>" title="<?php esc_html_e( 'Select Children', 'awebooking' ); ?>">
						<option value="0"><?php esc_html_e( 'Children', 'awebooking' ); ?></option>
						<?php print $occupancy_options; // WPCS: XSS OK. ?>
					</select>
				</p>
			<?php endif ?>

			<?php if ( abrs_infants_bookable() ) : ?>
				<p>
					<label class="screen-reader-text"><?php esc_html_e( 'Infants', 'awebooking' ); ?></label>
					<select name="<?php echo esc_attr( $input_prefix . '[infants]' ); ?>" title="<?php esc_html_e( 'Select Infants', 'awebooking' ); ?>">
						<option value="0"><?php esc_html_e( 'Infants', 'awebooking' ); ?></option>
						<?php print $occupancy_options; // WPCS: XSS OK. ?>
					</select>
				</p>
			<?php endif ?>
		</div>
	</td>

	<td>
		<span class="abrs-badge abrs-badge--primary"><?php abrs_price( $pricing->get_price() ); ?></span>

		<div class="book-actions">
			<button class="button button-primary abrs-button" name="submit" value="<?php echo esc_attr( $room_type->get_id() ); ?>"><?php echo esc_html__( 'Book', 'awebooking' ); ?></button>
		</div>
	</td>
</tr>
