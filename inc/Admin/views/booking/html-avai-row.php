<?php

$room_type = $avai->get_room_type();

$room_id = $room_type->get_id();

$remain_rooms = $avai->remain_rooms();
$reject_rooms = $avai->excluded_rooms();

$input_prefix = 'reservation_room[' . $room_type->get_id() . ']';

// Build the select occupancy options.
$occupancy_options = '';
for ( $i = 1; $i <= $room_type['maximum_occupancy']; $i++ ) {
	$occupancy_options .= '<option value="' . esc_attr( $i ) . '">' . esc_html( $i ) . '</option>';
}

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
		<a class="row-title" href="<?php echo esc_url( get_edit_post_link( $room_id ) ); ?>" target="_blank"><?php echo esc_html( $room_type->get_title() ); ?></a>

		<div class="">
			<span class="tippy" data-tippy-html="#js-debug-room-<?php echo esc_attr( $room_id ); ?>" data-tippy-delay="[100, 100000]" data-tippy-theme="abrs-tippy" data-tippy-interactive="true" data-tippy-arrow="true">
				<span class="dashicons dashicons-info"></span>
			</span>

			<?php
			/* translators: %d Number of rooms left */
			echo esc_html( sprintf( _n( '%d room left', '%d rooms left', count( $remain_rooms ), 'awebooking' ), count( $remain_rooms ) ) );
			?>
		</div>

		<div id="js-debug-room-<?php echo esc_attr( $room_id ); ?>" style="display: none;">
			<table class="debug-rooms__table">
				<?php
				foreach ( $remain_rooms as $room_info ) {
					echo '<tr class="debug-rooms--success">';
					echo '<th><span class="dashicons dashicons-yes"></span>', esc_html( $room_info['room']->get_name() ) ,'</th>';
					echo '<td>', esc_html( $room_info['reason_message'] ) ,'</td>';
					echo '</tr>';
				}

				foreach ( $reject_rooms as $room_info ) {
					echo '<tr class="debug-rooms--failure">';
					echo '<th><span class="dashicons dashicons-no-alt"></span>', esc_html( $room_info['room']->get_name() ) ,'</th>';
					echo '<td>', esc_html( $room_info['reason_message'] ) ,'</td>';
					echo '</tr>';
				}
				?>
			</table>
		</div>
	</td>

	<td>
		<div class="select-occupancy">
			<p>
				<label class="screen-reader-text"><?php esc_html_e( 'Adults', 'awebooking' ); ?></label>
				<select name="" title="<?php esc_html_e( 'Select Adults', 'awebooking' ); ?>">
					<option value="1"><?php esc_html_e( 'Adults', 'awebooking' ); ?></option>
					<?php print $occupancy_options; // WPCS: XSS OK. ?>
				</select>
			</p>

			<?php if ( abrs_children_bookable() ) : ?>
				<p>
					<label class="screen-reader-text"><?php esc_html_e( 'Children', 'awebooking' ); ?></label>
					<select name="" title="<?php esc_html_e( 'Select Children', 'awebooking' ); ?>">
						<option value="0"><?php esc_html_e( 'Children', 'awebooking' ); ?></option>
						<?php print $occupancy_options; // WPCS: XSS OK. ?>
					</select>
				</p>
			<?php endif ?>

			<?php if ( abrs_infants_bookable() ) : ?>
				<p>
					<label class="screen-reader-text"><?php esc_html_e( 'Infants', 'awebooking' ); ?></label>
					<select name="" title="<?php esc_html_e( 'Select Infants', 'awebooking' ); ?>">
						<option value="0"><?php esc_html_e( 'Infants', 'awebooking' ); ?></option>
						<?php print $occupancy_options; // WPCS: XSS OK. ?>
					</select>
				</p>
			<?php endif ?>
		</div>
	</td>

	<td>
		sdasdas
	</td>
</tr>
