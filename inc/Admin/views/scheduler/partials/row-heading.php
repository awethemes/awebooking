<?php
/* @vars $calendar */

use AweBooking\Support\Date_Utils as U;

?><div class="scheduler__row scheduler__row--heading">
	<?php foreach ( $calendar->get_period() as $day ) : ?>
		<div class="scheduler__column <?php echo esc_attr( implode( ' ', U::get_date_classes( $day ) ) ); ?>" title="<?php echo esc_attr( $day->format( 'l, M j, Y' ) ); ?>" data-date="<?php echo esc_attr( $day->format( 'Y-m-d' ) ); ?>">

			<span class="weekday"><?php echo esc_html( U::get_weekday_name( $day->dayOfWeek, 'abbrev' ) ); // @codingStandardsIgnoreLine ?></span>
			<span class="day"><?php echo esc_html( $day->format( 'd' ) ); ?></span>

			<?php if ( 1 == $day->day ) : ?>
				<span class="scheduler__month-label"><?php echo esc_html( $day->format( 'Y-m' ) ); ?></span>
			<?php endif ?>

		</div>
	<?php endforeach; ?>
</div><!-- /.scheduler__row -->
