<?php

$remain_rooms = $rooms->remains();
$reject_rooms = $rooms->excludes();

?><table class="debug-rooms__table">
	<?php
	foreach ( $remain_rooms as $room_info ) {
		echo '<tr class="debug-rooms--success">';
		echo '<th><span class="dashicons dashicons-yes"></span>', esc_html( $room_info['item']->get_name() ) ,'</th>';
		echo '<td>', esc_html( $room_info['message'] ) ,'</td>';
		echo '</tr>';
	}

	foreach ( $reject_rooms as $room_info ) {
		echo '<tr class="debug-rooms--failure">';
		echo '<th><span class="dashicons dashicons-no-alt"></span>', esc_html( $room_info['item']->get_name() ) ,'</th>';
		echo '<td>', esc_html( $room_info['message'] ) ,'</td>';
		echo '</tr>';
	}
	?>
</table>
