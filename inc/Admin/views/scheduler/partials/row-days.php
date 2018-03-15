<?php
/* @vars $calendar, $loop_calendar */

use AweBooking\Support\Date_Utils as U;

?><div class="scheduler__days">

	<?php foreach ( $calendar->get_period() as $day ) : ?>
		<div class="scheduler__column scheduler__date <?php echo esc_attr( implode( ' ', U::get_date_classes( $day ) ) ); ?>" data-date="<?php echo esc_attr( $day->format( 'Y-m-d' ) ); ?>">

			<span class="scheduler__datehover"></span>
			<?php $calendar->perform_call_method( 'display_day_column', $day, $loop_calendar ); ?>

		</div>
	<?php endforeach; ?>

</div><!-- /.scheduler__days -->
