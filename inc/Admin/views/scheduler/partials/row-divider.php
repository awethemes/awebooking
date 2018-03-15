<?php
/* @vars $calendar, $loop_scheduler */

use AweBooking\Support\Date_Utils as U;

?><div class="scheduler__row scheduler__row--divider">
	<div class="scheduler__days">

		<?php foreach ( $calendar->get_period() as $day ) : ?>
			<div class="scheduler__column scheduler__column--readonly <?php echo esc_attr( implode( ' ', U::get_date_classes( $day ) ) ); ?>" title="<?php echo esc_attr( $day->format( 'l, M j, Y' ) ); ?>" data-date="<?php echo esc_attr( $day->format( 'Y-m-d' ) ); ?>">

				<?php $calendar->perform_call_method( 'display_divider_column', $day, $loop_scheduler ); ?>
				<span>0</span><br><span>33</span>

			</div>
		<?php endforeach; ?>

	</div><!-- /.scheduler__days -->
</div><!-- /.scheduler__row -->
