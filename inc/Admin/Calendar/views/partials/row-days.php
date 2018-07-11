<?php
/**
 * Template displaying row days.
 *
 * @var \AweBooking\Admin\Calendar\Abstract_Scheduler $calendar
 * @var \AweBooking\Calendar\Calendar                 $loop_calendar
 *
 * @package AweBooking\Admin\Calendar
 */

if ( ! isset( $loop_scheduler ) ) {
	$loop_scheduler = null;
}

?><div class="scheduler__days">

	<?php foreach ( $calendar->period as $day ) : ?>
		<div class="scheduler__column scheduler__date <?php echo esc_attr( implode( ' ', abrs_date_classes( $day ) ) ); ?>" data-date="<?php echo esc_attr( $day->format( 'Y-m-d' ) ); ?>">

			<span class="scheduler__datehover"></span>
			<?php $calendar->call( 'display_day_column', $day, $loop_calendar, $loop_scheduler ); ?>

		</div>
	<?php endforeach; ?>

</div><!-- /.scheduler__days -->
