<?php

global $the_room_type;

// List all rooms.
$rooms = $the_room_type->get_rooms();

?>

<?php if ( abrs_blank( $rooms ) ) : ?>
	<label class="block-label"><?php esc_html_e( 'Generate rooms', 'awebooking' ); ?></label>

	<div class="abrs-input-addon" style="width: 150px;;">
		<select name="_scaffold_number_rooms" data-title="<?php echo esc_attr( $the_room_type->get( 'title' ) ); ?>">
			<?php for ( $i = 1; $i <= abrs_maximum_scaffold_rooms(); $i++ ) : ?>
				<option value="<?php echo esc_attr( $i ); ?>"><?php echo esc_html( $i ); ?></option>
			<?php endfor; ?>
		</select>

		<button type="button" class="button abrs-dashicons-button js-scaffold-rooms" title="<?php echo esc_html__( 'Generate rooms', 'awebooking' ); ?>">
			<span class="screen-reader-text"><?php echo esc_html__( 'Generate rooms', 'awebooking' ); ?></span>
			<span class="dashicons dashicons-update"></span>
		</button>
	</div>

	<!-- Content will be generate via JS -->
	<div class="abrs-sortable js-generated-rooms js-sorting-rooms" style="margin-top: 1em; display: none;"></div>

<?php else : ?>

	<ul class="abrs-sortable js-sorting-rooms">
		<?php foreach ( $rooms as $i => $room ) : ?>
			<li class="abrs-sortable__item">
				<div class="abrs-sortable__head">
					<span class="abrs-sortable__handle"></span>
					<span class="abrs-sortable__order"><?php echo esc_html( $i + 1 ); ?></span>
				</div>

				<div class="abrs-sortable__body">
					<input type="hidden" name="_rooms[<?php echo esc_attr( $room->get_id() ); ?>][id]" value="<?php echo esc_attr( $room->get_id() ); ?>">

					<strong class="screen-reader-text"><?php echo esc_html( $room['name'] ); ?></strong>
					<input type="text" name="_rooms[<?php echo esc_attr( $room->get_id() ); ?>][name]" value="<?php echo esc_attr( $room->get( 'name' ) ); ?>" class="transparent">
				</div>

				<div class="abrs-sortable__actions hidden">
					<?php /* translators: %s The room name */ ?>
					<a href="<?php echo esc_url( wp_nonce_url( abrs_admin_route( "/room/{$room->get_id()}" ), 'delete_room_' . $room->get_id() ) ); ?>" data-method="abrs-delete" title="<?php printf( esc_html__( 'Delete: %s', 'awebooking' ), esc_html( $room->get( 'name' ) ) ); ?>">
						<span class="screen-reader-text"><?php echo esc_html__( 'Delete', 'awebooking' ); ?></span>
						<span class="dashicons dashicons-no-alt"></span>
					</a>
				</div>
			</li>
		<?php endforeach ?>
	</ul>

<?php endif ?>

<script type="text/template" id="tmpl-template-room-item">
	<li class="abrs-sortable__item">
		<div class="abrs-sortable__head">
			<span class="abrs-sortable__handle"></span>
			<span class="abrs-sortable__order">{{ data.index }}</span>
		</div>

		<div class="abrs-sortable__body">
			<input type="hidden" name="{{ data.prefix }}[id]" value="{{ data.id }}">

			<strong class="screen-reader-text">{{ data.name }}</strong>
			<input type="text" name="{{ data.prefix }}[name]" value="{{ data.name }}" class="transparent">
		</div>

		<div class="abrs-sortable__actions hidden">
			<a href="#" data-method="abrs-delete">
				<span class="screen-reader-text"><?php echo esc_html__( 'Delete', 'awebooking' ); ?></span>
				<span class="dashicons dashicons-no-alt"></span>
			</a>
		</div>
	</li>
</script>
