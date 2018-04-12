<?php

$room_type = $avai->get_room_type();
$remain_rooms = $avai->remain_rooms();

?>

<tr>
	<td>
		<strong><?php echo esc_html( $room_type->get_title() ); ?></strong>

		<?php
		echo '<div class="afloat-right row-actions">';
		/* translators: %d Number of rooms left */
		echo esc_html( sprintf( _n( '%d room left', '%d rooms left', $remain_rooms->count(), 'awebooking' ), $remain_rooms->count() ) );
		// $this->print_rooms_debug( $room_type, $remain_rooms, $avai->excluded_rooms() );
		echo '</div>';
		?>

	</td>

	<td>
		<div class="abrs-timespan">
			<div class="abrs-timespan__start"></div>
			<div class="abrs-timespan__nights"></div>
			<div class="abrs-timespan__end"></div>
		</div>

		<span class="abrs-label abrs-label--info"><?php echo esc_html( $avai->timespan->nights() ); ?></span>
	</td>
</tr>
