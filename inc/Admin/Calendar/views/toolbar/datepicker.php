<?php

if ( wp_script_is( 'flatpickr', 'enqueued' ) ) {
	wp_enqueue_script( 'flatpickr' );
}

$next_date = $calendar->period->get_end_date();

?><div class="scheduler__toolbar scheduler__datepicker">
	<a href="#" title="<?php echo esc_html__( 'Prev Date', 'awebooking' ); ?>" class="scheduler__arrow prev">
		<span class="screen-reader-text"><?php echo esc_html_x( 'Next', 'next month', 'awebooking' ); ?></span>
		<i class="dashicons dashicons-arrow-left-alt2"></i>
	</a>

	<input type="text" class="flatpickr" value="<?php echo esc_attr( $calendar->datepoint ); ?>" readonly="true">

	<a href="#" title="<?php echo esc_html__( 'Next Date', 'awebooking' ); ?>" class="scheduler__arrow next">
		<span class="screen-reader-text"><?php echo esc_html_x( 'Prev', 'prev month', 'awebooking' ); ?></span>
		<i class="dashicons dashicons-arrow-right-alt2"></i>
	</a>
</div><!-- /.scheduler__toolbar -->

<script>
(function($, awebooking) {
	'use strict';

	$(function() {
		var plugin = window.awebooking || {};

		flatpickr('.scheduler__datepicker .flatpickr', {
			altInput: true,
			altFormat: (typeof plugin.i18n !== 'undefined') ? plugin.i18n.date_format : 'F j, Y',
			dateFormat: 'Y-m-d',
			onChange: function() {

			},
		});
	});

})(jQuery);
</script>
