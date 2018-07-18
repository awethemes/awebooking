<?php
/* @vars $calr, $event, $calendar, $scheduler, $attributes */

$unblock_atts = [
	'data-room'       => $calendar->get_uid(),
	'data-start-date' => $event->get_start_date()->format( 'Y-m-d' ),
	'data-end-date'   => $event->get_end_date()->format( 'Y-m-d' ),
];

?>
<div <?php echo abrs_html_attributes( $attributes ); // WPCS: XSS OK. ?>>
	<div style="display: none;">
		<div class="js-tippy-html abrs-ptb1">
			<span><?php echo esc_html__( 'Period is blocked', 'awebooking' ); ?></span> &middot;
			<a href="#" class="js-unlock-period" <?php echo abrs_html_attributes( $unblock_atts ); // WPCSS: XSS OK. ?>><?php echo esc_html__( 'Unblock', 'awebooking' ); ?></a>
		</div>
	</div>
</div>
