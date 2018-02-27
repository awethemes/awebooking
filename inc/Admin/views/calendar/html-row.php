<?php
/* @vars $cal, $calendar, $period */

use AweBooking\Support\Utils as U;

$events = U::rescue( function () use ( $cal, $calendar, $period ) {
	return $cal->get_calendar_events( $calendar, $period );
});

?><div class="awebooking-schedule__row" data-calendar="<?php echo esc_attr( $calendar->get_uid() ); ?>">

	<div class="awebooking-schedule__days">
		<?php foreach ( $period as $date ) : ?>
			<div class="awebooking-schedule__column <?php echo esc_attr( implode( ' ', $cal->get_date_classes( $date, 'awebooking-schedule__day' ) ) ); ?>" data-date="<?php echo esc_attr( $date->format( 'Y-m-d' ) ); ?>">
				<span class="awebooking-schedule__day-hover"></span>

				<?php $cal->cell_date_contents( $date, $calendar ); // WPCS: XSS OK. ?>
			</div>
		<?php endforeach; ?>
	</div>

	<?php if ( ! is_null( $events ) ) : ?>

		<div class="awebooking-schedule__events">
			<?php foreach ( $period as $date ) : ?>
				<div class="awebooking-schedule__column awebooking-schedule__cell-event" data-date="<?php echo esc_attr( $date->format( 'Y-m-d' ) ); ?>">
					<?php $cal->cell_event_contents( $events, $date, $calendar ); // WPCS: XSS OK. ?>
				</div>
			<?php endforeach ?>
		</div>

	<?php endif; ?>
</div>
