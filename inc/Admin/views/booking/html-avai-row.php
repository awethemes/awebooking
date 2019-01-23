<?php

list ($room_type, $room_rate ) = array_values( $avai );

// Build the input prefix.
$input_prefix = 'reservation[' . $room_type->get_id() . ']';

// Build the select occupancy options.
$occupancy_options = function ( $min = 1, $selected = 0 ) use ( $room_type ) {
	$html = '';

	for ( $i = $min; $i <= $room_type->get( 'maximum_occupancy' ); $i++ ) {
		$html .= '<option value="' . esc_attr( $i ) . '" ' . selected( $selected, $i, false ) . '>' . esc_html( $i ) . '</option>';
	}

	return $html;
};

?>

<tr>
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

		<span class="text-remain-rooms">
			<?php
			$remain_rooms = $room_rate->get_remain_rooms();

			/* translators: %d Number of rooms left */
			echo esc_html( sprintf( _n( '%d room left', '%d rooms left', count( $remain_rooms ), 'awebooking' ), count( $remain_rooms ) ) );
			?>
			<span class="tippy" data-tippy-html="#js-debug-room-<?php echo esc_attr( $room_type->get_id() ); ?>" data-tippy-theme="abrs-tippy" data-tippy-size="large" data-tippy-max-width="350px;" data-tippy-interactive="true" data-tippy-arrow="true">
				<span class="dashicons dashicons-info"></span>
			</span>
		</span>

		<div id="js-debug-room-<?php echo esc_attr( $room_type->get_id() ); ?>" style="display: none;">
			<?php abrs_admin_template_part( 'booking/html-debug-rooms.php', compact( 'room_rate' ) ); ?>
		</div>
	</td>

	<td>
		<select name="<?php echo esc_attr( $input_prefix . '[room]' ); ?>">
			<?php foreach ( $remain_rooms as $room_info ) : ?>
			<option value="<?php echo esc_html( abrs_optional( $room_info['resource'] )->get_id() ); ?>"><?php echo esc_html( abrs_optional( $room_info['resource'] )->get( 'name' ) ); ?></option>
			<?php endforeach ?>
		</select>
	</td>

	<td>
		<div class="wrap-select-occupancy">
			<p>
				<label class="screen-reader-text"><?php esc_html_e( 'Adults', 'awebooking' ); ?></label>
				<select name="<?php echo esc_attr( $input_prefix . '[adults]' ); ?>" title="<?php esc_html_e( 'Select adults', 'awebooking' ); ?>">
					<?php print $occupancy_options( 1, $res_request->adults ); // WPCS: XSS OK. ?>
				</select>
			</p>

			<?php if ( abrs_children_bookable() ) : ?>
				<p>
					<label class="screen-reader-text"><?php esc_html_e( 'Children', 'awebooking' ); ?></label>
					<select name="<?php echo esc_attr( $input_prefix . '[children]' ); ?>" title="<?php esc_html_e( 'Select children', 'awebooking' ); ?>">
						<?php print $occupancy_options( 0, $res_request->children ); // WPCS: XSS OK. ?>
					</select>
				</p>
			<?php endif ?>

			<?php if ( abrs_infants_bookable() ) : ?>
				<p>
					<label class="screen-reader-text"><?php esc_html_e( 'Infants', 'awebooking' ); ?></label>
					<select name="<?php echo esc_attr( $input_prefix . '[infants]' ); ?>" title="<?php esc_html_e( 'Select infants', 'awebooking' ); ?>">
						<?php print $occupancy_options( 0, $res_request->infants ); // WPCS: XSS OK. ?>
					</select>
				</p>
			<?php endif ?>
		</div>
	</td>

	<td>
		<span class="abrs-badge abrs-badge--primary tippy" data-tippy-html="#js-breakdown-<?php echo esc_attr( $room_type->get_id() ); ?>" data-tippy-theme="abrs-tippy" data-tippy-size="large" data-tippy-max-width="350px;" data-tippy-interactive="true" data-tippy-arrow="true">
			<?php abrs_price( $room_rate->get_rate() ); ?>
		</span>

		<input type="hidden" name="<?php echo esc_attr( $input_prefix . '[total]' ); ?>" value="<?php echo esc_attr( $room_rate->get_rate() ); ?>">

		<div class="book-actions">
			<button class="button button-primary abrs-button" name="submit" value="<?php echo esc_attr( $room_type->get_id() ); ?>"><?php echo esc_html__( 'Book', 'awebooking' ); ?></button>
		</div>

		<div id="js-breakdown-<?php echo esc_attr( $room_type->get_id() ); ?>" style="display: none;">
			<?php if ( $room_rate->breakdown ) : ?>
			<table class="awebooking-table abrs-breakdown-table">
				<tbody>
					<?php foreach ( $room_rate->breakdown as $date => $amount ) : ?>
						<tr>
							<td class="abrs-text-left"><?php echo abrs_format_date( $date ); // WPCS: XSS OK. ?></td>
							<td class="abrs-text-right"><?php abrs_price( $amount ); ?></td>
						</tr>
					<?php endforeach ?>
				</tbody>
			</table>
			<?php endif; ?>
		</div>
	</td>
</tr>
