<?php
/* @vars $calendar, $loop_scheduler */

?><div class="scheduler__row scheduler__row--divider">
	<div class="scheduler__days">

		<?php foreach ( $calendar->period as $day ) : ?>
			<div class="scheduler__column scheduler__column--readonly <?php echo esc_attr( implode( ' ', abrs_date_classes( $day ) ) ); ?>" data-date="<?php echo esc_attr( $day->format( 'Y-m-d' ) ); ?>">

				<?php $calendar->call( 'display_divider_column', $day, $loop_scheduler ); ?>

			</div>
		<?php endforeach; ?>

	</div><!-- /.scheduler__days -->
</div><!-- /.scheduler__row -->
