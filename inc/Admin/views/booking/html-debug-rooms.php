<?php

$rooms_avai = $room_rate->get_availability();

?><table class="debug-rooms__table">
	<?php
	foreach ( $rooms_avai->remains() as $room_info ) {
		echo '<tr class="debug-rooms--success">';
		echo '<th><span class="dashicons dashicons-yes"></span>', esc_html( $room_info['resource']->get_name() ) ,'</th>';
		echo '<td>', esc_html( $room_info['message'] ) ,'</td>';
		echo '</tr>';
	}

	foreach ( $rooms_avai->excludes() as $room_info ) {
		echo '<tr class="debug-rooms--failure">';
		echo '<th><span class="dashicons dashicons-no-alt"></span>', esc_html( $room_info['resource']->get_name() ) ,'</th>';
		echo '<td>', esc_html( $room_info['message'] ) ,'</td>';
		echo '</tr>';
	}
	?>
</table>
