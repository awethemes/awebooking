<div class="cmb-row">
	<div class="cmb-th">
		<label for="number_of_rooms"><?php echo esc_html( $field->prop( 'name' ) ); ?></label>
	</div>

	<div class="cmb-td">
		<div id="abkng-rooms">

			<input type="text" class="cmb2-text-small" id="number_of_rooms" v-model="totalRooms">
			<button class="button" type="button" @click.prevent="regenerateRooms()">Update</button>

			<!-- <a href="#" class="button button-primary">Edit rooms</a> -->

			<ul class="list-rooms clear">
				<li v-for="(room, index) in rooms" class="abkng-inline-room" :class="{ 'new-room': (room.id <= 0) }">
					<span>{{ index + 1 }}</span>

					<input type="hidden" :name="'abkng_rooms[' + index + '][id]'" :value="room.id">
					<input type="text" :name="'abkng_rooms[' + index + '][name]'" :value="room.name" v-model="room.name">

					<button type="button" @click.prevent="deleteRoomByIndex(index)">&times;</button>
				</li>
			</ul>

		</div>
	</div>
</div>

<script type="text/javascript">
	window.ABKNG_ROOMS = <?php echo json_encode( $room_type->get_rooms() ); ?>;
</script>
