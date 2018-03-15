<div class="wrap">
	<h1 class="wp-heading-inline"><?php esc_html_e( 'Calendar', 'awebooking' ); ?></h1>
	<a href="<?php echo esc_url( awebooking( 'url' )->admin_route( 'reservation' ) ); ?>" class="page-title-action"><?php echo esc_html__( 'New Reservation', 'awebooking' ); ?></a>

	<hr class="wp-header-end">

	<div style="padding-top: 1em;"></div>
	<?php $scheduler->display(); ?>
</div>

<script type="text/javascript">
(function($) {
	'use strict';

	$(function() {
		var awebooking = window.TheAweBooking;

		// Create the scheduler.
		var scheduler = new awebooking.ScheduleCalendar({
			el: '.scheduler',
		});

		$('.awebooking-scheduler__event').each(function() {
			var $popper = $(this).find('.popper');

			var popper = new TheAweBooking.Popper(this, $popper[0], {
				placement: 'top',
				modifiers: {
					flip: { enabled: false },
					hide: { enabled: false },
					preventOverflow: { enabled: false }
				}
			});

			$(this).on('mouseenter', function(e) {
				popper.update();
				$popper.show();
			}).on( 'mouseleave', function() {
				$popper.hide();
			});
		});
	});

})(jQuery);
</script>
