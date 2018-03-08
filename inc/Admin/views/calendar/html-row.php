<?php
/* @vars $cal, $calendar, $period */

use AweBooking\Support\Utils as U;

$events = U::rescue( function () use ( $cal, $calendar, $period ) {
	return $cal->get_calendar_events( $calendar, $period );
});

?>

<div class="scheduler__row" data-calendar="<?php echo esc_attr( $calendar->get_uid() ); ?>">
	<div class="scheduler__days">
		<?php foreach ( $period as $date ) : ?>
			<div class="scheduler__column scheduler__date <?php echo esc_attr( implode( ' ', $cal->get_date_classes( $date ) ) ); ?>" data-date="<?php echo esc_attr( $date->format( 'Y-m-d' ) ); ?>">
				<span class="scheduler__datehover"></span>
				<?php $cal->cell_date_contents( $date, $calendar ); // WPCS: XSS OK. ?>
			</div>
		<?php endforeach; ?>
	</div>

	<?php if ( ! is_null( $events ) ) : ?>

		<div class="scheduler__events">
			<div class="scheduler__events-row">
				<?php foreach ( $period as $date ) : ?>
					<div class="scheduler__column scheduler__column--event" data-date="<?php echo esc_attr( $date->format( 'Y-m-d' ) ); ?>">
						<?php $cal->cell_event_contents( $events, $date, $calendar ); // WPCS: XSS OK. ?>
					</div>
				<?php endforeach ?>
			</div>
		</div>

	<?php endif; ?>

</div>
