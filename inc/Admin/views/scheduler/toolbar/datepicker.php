<?php
/* @vars $calendar */

use AweBooking\Support\Date_Utils as U;

$date = $calendar->get_datepoint();

?>

<div class="scheduler__toolbar scheduler__datepicker">
	<a href="#" title="<?php echo esc_html__( 'Prev Date', 'awebooking' ); ?>" class="scheduler__arrow prev">
		<span class="screen-reader-text"><?php echo esc_html_x( 'Next', 'next month', 'awebooking' ); ?></span>
		<i class="dashicons dashicons-arrow-left-alt2"></i>
	</a>

	<input type="text" class="flatpickr" value="<?php echo esc_attr( $date->toDateString() ); ?>" readonly="true">

	<a href="#" title="<?php echo esc_html__( 'Next Date', 'awebooking' ); ?>" class="scheduler__arrow next">
		<span class="screen-reader-text"><?php echo esc_html_x( 'Prev', 'prev month', 'awebooking' ); ?></span>
		<i class="dashicons dashicons-arrow-right-alt2"></i>
	</a>
</div><!-- /.scheduler__toolbar -->
