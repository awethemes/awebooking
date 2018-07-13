<?php
/**
 * HTML displaying rooms in a room type.
 *
 * @var \AweBooking\Model\Room_Type $the_room_type
 *
 * @package AweBooking
 */

global $the_room_type, $post_id;

// List all rooms.
$rooms = $the_room_type->get_rooms();

// In translation, we can not do some tasks like edit or delete room unit.
$is_translation = null;
if ( abrs_running_on_multilanguage() ) {
	$is_translation = abrs_multilingual()->get_original_post( $post_id ) != $post_id;
}

?>

<div id="js-rooms-list" style="max-width: 500px;">
	<?php if ( $is_translation ) : ?>

		<ul class="abrs-sortable">
			<?php foreach ( $rooms as $i => $room ) : ?>
				<li class="abrs-sortable__item">
					<div class="abrs-sortable__head"><span class="abrs-sortable__order"><?php echo esc_html( $i + 1 ); ?></span></div>
					<div class="abrs-sortable__body"><strong><?php echo esc_html( $room->get( 'name' ) ); ?></strong></div>
				</li>
			<?php endforeach; ?>
		</ul>

		<p><?php echo esc_html__( 'Note: This is a translation of room type, you can not perform any action to this.', 'awebooking' ); ?></p>

	<?php else : ?>
		<div class="abrs-input-addon" data-bind="visible: rooms().length === 0" style="width: 150px; display: none;">
			<select data-bind="value: scaffoldNumber" data-title="<?php echo esc_attr( $the_room_type->get( 'title' ) ); ?>">
				<?php $max_scaffold_rooms = abrs_maximum_scaffold_rooms(); ?>
				<?php for ( $i = 1; $i <= $max_scaffold_rooms; $i++ ) : ?>
					<option value="<?php echo esc_attr( $i ); ?>" <?php selected( 3, $i ); ?>><?php echo esc_html( $i ); ?></option>
				<?php endfor; ?>
			</select>

			<button type="button" data-bind="click: scaffold" class="button abrs-dashicons-button" title="<?php echo esc_html__( 'Generate rooms', 'awebooking' ); ?>">
				<span class="screen-reader-text"><?php echo esc_html__( 'Generate rooms', 'awebooking' ); ?></span>
				<span class="dashicons dashicons-update"></span>
			</button>
		</div>

		<ul data-bind="foreach: rooms, visible: rooms().length > 0" class="abrs-sortable js-sorting-rooms" style="display: none;">
			<li class="abrs-sortable__item">
				<div class="abrs-sortable__head">
					<span class="abrs-sortable__handle"></span>
					<span class="abrs-sortable__order" data-bind="text: (-1 == id ? '*' : $index() + 1)"></span>
				</div>

				<div class="abrs-sortable__body">
					<input type="hidden" data-bind="value: $data.id, attr: { name: '_rooms[' + $index() + '][id]' }">
					<input type="text" data-bind="value: $data.name, attr: { name: '_rooms[' + $index() + '][name]' }" class="transparent" >
				</div>

				<div class="abrs-sortable__actions hidden">
					<a href="#" data-bind="click: $root.remove.bind($root)">
						<span class="screen-reader-text"><?php echo esc_html__( 'Delete', 'awebooking' ); ?></span>
						<span class="dashicons dashicons-no-alt"></span>
					</a>
				</div>
			</li>
		</ul>

		<button type="button" data-bind="click: add.bind($root), visible: rooms().length > 0" class="button button abrs-button abrs-mt1" style="display: none;"><?php esc_html_e( 'Add room', 'awebooking' ); ?></button>
		<script>
			var _awebookingRooms = <?php echo json_encode( [
				'rooms'       => $rooms,
				'deleteNonce' => wp_create_nonce( 'delete_room' ),
			] ); ?>;
		</script>
	<?php endif; ?>
</div>
