<?php
/* @vars $calendar, $loop_calendar */

?><div class="scheduler__days">

	<?php foreach ( $calendar->period as $day ) : ?>
		<div class="scheduler__column scheduler__date <?php echo esc_attr( implode( ' ', abrs_date_classes( $day ) ) ); ?>" data-date="<?php echo esc_attr( $day->format( 'Y-m-d' ) ); ?>">

			<span class="scheduler__datehover"></span>
			<?php $calendar->call( 'display_day_column', $day, $loop_calendar ); ?>

		</div>
	<?php endforeach; ?>

</div><!-- /.scheduler__days -->
