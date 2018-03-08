<?php

use League\Period\Period;
use AweBooking\Support\Formatting as format;

$current_route = awebooking( 'url' )->full();

list( $prev_date, $next_date ) = [
	$period->get_start_date()->sub( $period->getDateInterval() ),
	$period->get_end_date()->add( $period->getDateInterval() ),
];

?>

<div class="scheduler awebooking-scheduler">

	<div class="scheduler__header">
		<div class="scheduler__header-aside">
			<div class="scheduler__legends">
				<span data-init="awebooking-tooltip" title="<?php echo esc_html__( 'Today', 'awebooking' ); ?>" style="background-color: #fdf7c3"></span>
				<span data-init="awebooking-tooltip" title="<?php echo esc_html__( 'Weekend', 'awebooking' ); ?>" style="background-color: #eee"></span>
				<span data-init="awebooking-tooltip" title="<?php echo esc_html__( 'Greater than base price', 'awebooking' ); ?>" style="background-color: #c9e4a9">></span>
				<span data-init="awebooking-tooltip" title="<?php echo esc_html__( 'Less than base price', 'awebooking' ); ?>" style="background-color: #ff837a">></span>
			</div>
		</div>

		<div class="scheduler__header-main">
			<div class="scheduler-flex">
				<div class="scheduler-flexspace"></div>

				<div class="scheduler__datepicker">
					<a href="<?php echo esc_url( add_query_arg( 'date', $prev_date->toDateString(), $current_route ) ); ?>" title="<?php echo esc_html__( 'Prev Date', 'awebooking' ); ?>" class="scheduler__arrow prev">
						<span class="screen-reader-text"><?php echo esc_html_x( 'Next', 'next month', 'awebooking' ); ?></span>
						<i class="dashicons dashicons-arrow-left-alt2"></i>
					</a>

					<input type="text" data-input="true" class="flatpickr" value="<?php echo esc_attr( $date->toDateString() ); ?>" readonly="true">

					<a href="<?php echo esc_url( add_query_arg( 'date', $next_date->toDateString(), $current_route ) ); ?>" title="<?php echo esc_html__( 'Next Date', 'awebooking' ); ?>" class="scheduler__arrow next">
						<span class="screen-reader-text"><?php echo esc_html_x( 'Prev', 'prev month', 'awebooking' ); ?></span>
						<i class="dashicons dashicons-arrow-right-alt2"></i>
					</a>
				</div>

			</div>
		</div>
	</div>

	<div class="scheduler__container">
		<aside class="scheduler__aside">
			<span class="scheduler__month-label"><?php echo esc_html( $date->format( 'Y-m' ) ); ?></span>

			<div class="scheduler__aside-heading">
				<?php if ( $calendar_name = $scheduler->get_name() ) : ?>
					<h2><?php echo esc_html( $calendar_name ); ?></h2>
				<?php endif ?>
			</div>

			<ul class="scheduler__menus">
				<?php foreach ( $scheduler as $_scheduler ) : ?>
					<li>
						<div class="scheduler__menu">
							<i class="afc afc-bed"></i>
							<strong class="togglename" data-target="<?php echo esc_attr( $_scheduler->get_reference()->get_id() ); ?>"><?php echo esc_html( $_scheduler->get_name() ); ?></strong>

							<nav class="scheduler__nav-actions">
								<a href="#" target="_blank">
									<span class="screen-reader-text"><?php esc_html_e( 'Show', 'awebooking' ); ?></span>
									<span class="dashicons dashicons-external"></span>
								</a>
							</nav>
						</div>

						<ul class="schedule__submenu" data-schedule="<?php echo esc_attr( $_scheduler->get_reference()->get_id() ); ?>">
						<?php foreach ( $_scheduler as $calendar ) : ?>

							<li class="scheduler__menu" data-calendar="<?php echo esc_attr( $calendar->get_uid() ); ?>">
								<span><?php echo esc_html( $calendar->get_name() ); ?></span>
							</li>

						<?php endforeach ?>
						</ul>
					</li>
				<?php endforeach ?>
			</ul>

		</aside><!-- /.scheduler__aside -->


		<div class="scheduler__main">

			<header class="scheduler__heading">
				<div class="scheduler__row">

					<?php foreach ( $period as $date ) : ?>
						<div class="scheduler__column <?php echo esc_attr( implode( ' ', $cal->get_date_classes( $date ) ) ); ?>" title="<?php echo esc_attr( $date->format( $cal->get_option( 'date_title' ) ) ); ?>" data-date="<?php echo esc_attr( $date->format( 'Y-m-d' ) ); ?>">
							<span class="weekday"><?php echo esc_html( $cal->get_weekday_name( $date->dayOfWeek, 'abbrev' ) ); // @codingStandardsIgnoreLine ?></span>
							<span class="day"><?php echo esc_html( $date->format( 'd' ) ); ?></span>

							<?php if ( 1 == $date->day ) : ?>
								<span class="scheduler__month-label"><?php echo esc_html( $date->format( 'Y-m' ) ); ?></span>
							<?php endif ?>
						</div>
					<?php endforeach; ?>

				</div>
			</header><!-- /.scheduler__heading -->

			<div class="scheduler__body">
				<?php foreach ( $scheduler as $_scheduler ) : ?>

					<?php $this->partial( 'calendar/html-divider-row.php', compact( 'cal', '_scheduler', 'period' ) ); ?>

					<section class="schedule__section" data-schedule="<?php echo esc_attr( $_scheduler->get_reference()->get_id() ); ?>">

						<?php foreach ( $_scheduler as $calendar ) : ?>

							<?php $this->partial( 'calendar/html-row.php', compact( 'cal', 'calendar', 'period' ) ); ?>

						<?php endforeach ?>

					</section>

				<?php endforeach ?>

				<div class="scheduler__marker" style="display: none;">
					<span class="scheduler__markerspan">1</span>
				</div>
			</div><!-- /.scheduler__body -->

		</div>

	</div><!-- /.scheduler__container -->

	<div class="scheduler__popper popper" style="display: none;">
		<div class="popper__arrow" x-arrow></div>

		<ul class="schedule__actions">
			<li><a href="#" data-schedule-action="set-price"><i class="afc afc-dollar-sign"></i><span><?php echo esc_html__( 'Adjust Price', 'awebooking' ); ?></span></a></li>
			<li><a href="#" data-schedule-action="reset-price"><i class="dashicons dashicons-image-rotate"></i><span><?php echo esc_html__( 'Revert Price', 'awebooking' ); ?></span></a></li>
		</ul>
	</div>

</div><!-- /.awebooking-schedule -->

<script type="text/javascript">
(function($) {
	'use strict';

	$(function() {
		var awebooking = window.TheAweBooking;

		// Create the scheduler.
		var scheduler = new awebooking.ScheduleCalendar({
			el: '.awebooking-scheduler',
		});

		$('.togglename').on('click', function() {
			$target = $(this).data('target');
			$('[data-schedule="'+$target+'"]').toggleClass('hidden');
		});

		$('#schedule-datepicker').datepicker({
			dateFormat: 'yy-mm-dd',
			beforeShow: function() {
				$('#ui-datepicker-div').addClass('cmb2-element');
			},
		}).on('change', function() {
			$('#schedule-datepicker-alt').html( $(this).val() );
		});

		$('.awebooking-schedule__event').each(function() {
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
