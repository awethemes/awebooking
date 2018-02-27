<?php
/* @vars $cal, $_scheduler */

?><div class="awebooking-schedule__row awebooking-schedule__row--divider">
	<div class="awebooking-schedule__days">
		<?php foreach ( $period->get_date_period() as $date ) : ?>
			<div class="awebooking-schedule__column <?php echo esc_attr( implode( ' ', $cal->get_date_classes( $date, 'awebooking-schedule__day' ) ) ); ?>" data-date="<?php echo esc_attr( $date->toDateString() ); ?>">

			</div>
		<?php endforeach; ?>
	</div>

	<div class="awebooking-schedule__events">
		<?php foreach ( $period->get_date_period() as $date ) : ?>
			<div class="awebooking-schedule__column awebooking-schedule__cell-event" data-date="<?php echo esc_attr( $date->toDateString() ); ?>">
				<span class="awebooking-schedule__amount">$0</span>
			</div>
		<?php endforeach ?>
	</div>
</div>
