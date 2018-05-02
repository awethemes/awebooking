<?php

if ( wp_script_is( 'flatpickr', 'enqueued' ) ) {
	wp_enqueue_script( 'flatpickr' );
}

// Get current full URL.
$current_url = abrs_http_request()->full_url();

// Create prev & next date, alway +2 days in
// start date because we display the scheduler
// from last 2 days from selected day.
$prev_date = $calendar->period
	->moveStartDate( '+2 days' )
	->previous()
	->get_start_date()
	->format( 'Y-m-d' );

$next_date = $calendar->period
	->moveStartDate( '+2 days' )
	->get_end_date()
	->format( 'Y-m-d' );

?><div class="scheduler__toolbar scheduler__datepicker">
	<?php if ( ! $calendar->period->contains( 'today' ) ) : ?>
		<a href="<?php echo esc_url( rawurldecode( add_query_arg( 'date', 'today', $current_url ) ) ); ?>" class="button abrs-button" style="margin-top: 3px; margin-right: 5px;"><?php echo esc_html__( 'Today', 'awebooking' ); ?></a>
	<?php endif ?>

	<a href="<?php echo esc_url( rawurldecode( add_query_arg( 'date', $prev_date, $current_url ) ) ); ?>" title="<?php echo esc_html__( 'Previous Date', 'awebooking' ); ?>" class="scheduler__arrow prev">
		<span class="screen-reader-text"><?php echo esc_html_x( 'Previous', 'prev month', 'awebooking' ); ?></span>
		<i class="dashicons dashicons-arrow-left-alt2"></i>
	</a>

	<input type="text" class="flatpickr" value="<?php echo esc_attr( $calendar->datepoint ); ?>" readonly="true">

	<a href="<?php echo esc_url( rawurldecode( add_query_arg( 'date', $next_date, $current_url ) ) ); ?>" title="<?php echo esc_html__( 'Next Date', 'awebooking' ); ?>" class="scheduler__arrow next">
		<span class="screen-reader-text"><?php echo esc_html_x( 'Next', 'next month', 'awebooking' ); ?></span>
		<i class="dashicons dashicons-arrow-right-alt2"></i>
	</a>
</div><!-- /.scheduler__toolbar -->

<script>
(function($, awebooking) {
	'use strict';

	$(function() {
		var plugin = window.awebooking || {};

		var onSelectedDate = function(dates, dateStr) {
			setTimeout(function() {
				window.location.href = plugin.utils.addQueryArgs({ date: dateStr });
			}, 500);
		};

		flatpickr('.scheduler__datepicker .flatpickr', {
			altInput: true,
			altFormat: (typeof plugin.i18n !== 'undefined') ? plugin.i18n.date_format : 'F j, Y',
			dateFormat: 'Y-m-d',
			onChange: onSelectedDate,
		});
	});

})(jQuery);
</script>
