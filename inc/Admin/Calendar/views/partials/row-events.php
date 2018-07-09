<?php
/* @vars $calendar, $loop_calendar */

if ( ! isset( $loop_scheduler ) ) {
	$loop_scheduler = null;
}

?><div class="scheduler__events">
	<div class="scheduler__events-row">

		<?php foreach ( $calendar->period as $day ) : ?>
			<div class="scheduler__column scheduler__column--event" data-date="<?php echo esc_attr( $day->format( 'Y-m-d' ) ); ?>">
				<?php $calendar->call( 'display_event_column', $day, $loop_calendar, $loop_scheduler ); ?>
			</div><!-- /.scheduler__column -->
		<?php endforeach ?>

	</div><!-- /.scheduler__events-row -->
</div><!-- /.scheduler__events -->
