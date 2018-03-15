<?php
/* @vars $calendar, $loop_calendar */

use AweBooking\Support\Date_Utils as U;

?><div class="scheduler__events">
	<div class="scheduler__events-row">

		<?php foreach ( $calendar->get_period() as $day ) : ?>
			<div class="scheduler__column scheduler__column--event" data-date="<?php echo esc_attr( $day->format( 'Y-m-d' ) ); ?>">
				<?php $calendar->perform_call_method( 'display_event_column', $day, $loop_calendar ); ?>
			</div><!-- /.scheduler__column -->
		<?php endforeach ?>

	</div><!-- /.scheduler__events-row -->
</div><!-- /.scheduler__events -->
