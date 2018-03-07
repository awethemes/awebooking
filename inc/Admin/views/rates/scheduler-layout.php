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
		<div class="scheduler__legend"></div>

		<div class="scheduler__header-block">
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
			<div class="scheduler__aside-heading">
				<?php if ( $calendar_name = $scheduler->get_name() ) : ?>
					<h2><?php echo esc_html( $calendar_name ); ?></h2>
				<?php endif ?>
			</div>

			<ul class="scheduler__menus">
				<?php foreach ( $scheduler as $calendar ) : ?>
					<li class="scheduler__menu">
						<strong class="scheduler__calendar-title">
							<i class="afc afc-bed"></i>
							<span><?php echo esc_html( $calendar->get_name() ); ?></span>
						</strong>

						<nav class="scheduler__nav-actions">
							<a href="<?php echo esc_url( get_edit_post_link( $calendar->get_resource()->get_id() ) ); ?>" target="_blank">
								<span class="screen-reader-text"><?php esc_html_e( 'Show', 'awebooking' ); ?></span>
								<span class="dashicons dashicons-external"></span>
							</a>
						</nav>
					</li>
				<?php endforeach ?>
			</ul>
		</aside><!-- /.scheduler__aside -->

		<div class="scheduler__main">

			<header class="scheduler__heading">
				<div class="scheduler__row">

					<?php foreach ( $period as $date ) : ?>
						<div class="scheduler__column scheduler__date <?php echo esc_attr( implode( ' ', $cal->get_date_classes( $date ) ) ); ?>" title="<?php echo esc_attr( $date->format( $cal->get_option( 'date_title' ) ) ); ?>" data-date="<?php echo esc_attr( $date->format( 'Y-m-d' ) ); ?>">
							<span class="weekday"><?php echo esc_html( $cal->get_weekday_name( $date->dayOfWeek, 'abbrev' ) ); // @codingStandardsIgnoreLine ?></span>
							<span class="day"><?php echo esc_html( $date->format( 'd' ) ); ?></span>

							<?php if ( 1 == $date->day ) : ?>
								<span class="newmonth"><?php echo esc_html( $date->format( 'Y-m' ) ); ?></span>
							<?php endif ?>
						</div>
					<?php endforeach; ?>

				</div>
			</header><!-- /.scheduler__heading -->

			<div class="scheduler__body">
				<?php foreach ( $scheduler as $calendar ) : ?>
					<div class="scheduler__row" data-calendar="<?php echo esc_attr( $calendar->get_uid() ); ?>">

						<div class="scheduler__days">
							<?php foreach ( $period as $date ) : ?>
								<div class="scheduler__column scheduler__date <?php echo esc_attr( implode( ' ', $cal->get_date_classes( $date ) ) ); ?>" data-date="<?php echo esc_attr( $date->format( 'Y-m-d' ) ); ?>">
									<span class="scheduler__datehover"></span>
									<?php $cal->cell_date_contents( $date, $calendar ); // WPCS: XSS OK. ?>
								</div>
							<?php endforeach; ?>
						</div>

						<div class="scheduler__events">
							<div class="scheduler__events-row">
								<?php list( $base_amount, $pricing ) = $list_pricing[ $calendar->get_uid() ]; ?>

								<?php foreach ( $period as $date ) : ?>
									<?php $amount = $pricing->get( $date->format( 'Y-m-d' ) )->get_amount(); ?>
									<div class="scheduler__column scheduler__event" data-date="<?php echo esc_attr( $date->format( 'Y-m-d' ) ); ?>">
										<?php if ( $amount->greater_than( $base_amount ) ) : ?>
											<span class="scheduler__rate-state stateup"><i class="dashicons dashicons-arrow-up"></i></span>
										<?php elseif ( $amount->less_than( $base_amount ) ) : ?>
											<span class="scheduler__rate-state statedown"><i class="dashicons dashicons-arrow-down"></i></span>
										<?php endif ?>

										<span class="scheduler__rate-amount">
											<span><?php echo format::money( $amount, true ); // WPCS: XSS OK. ?></span>
										</span>
									</div>
								<?php endforeach ?>
							</div>
						</div>

					</div>
				<?php endforeach ?>

				<div class="scheduler__marker" style="display: none;">
					<span class="scheduler__markerspan">1</span>
				</div>
			</div><!-- /.scheduler__body -->

		</div><!-- /.scheduler__main -->
	</div><!-- /.scheduler__container -->

	<div class="scheduler__popper popper" style="display: none;">
		<div class="popper__arrow" x-arrow></div>

		<ul class="schedule__actions">
			<li><a href="#" data-schedule-action="set-price"><i class="afc afc-dollar-sign"></i><span><?php echo esc_html__( 'Adjust Price', 'awebooking' ); ?></span></a></li>
			<li><a href="#" data-schedule-action="reset-price"><i class="dashicons dashicons-image-rotate"></i><span><?php echo esc_html__( 'Revert Price', 'awebooking' ); ?></span></a></li>
		</ul>
	</div>

</div><!-- /.awebooking-scheduler -->

<div id="scheduler-form-dialog" class="awebooking-dialog" title="<?php echo esc_html__( 'Adjust Price', 'awebooking' ); ?>" style="display: none;">
	<form id="scheduler-form" method="POST" action="<?php echo esc_url( awebooking( 'url' )->admin_route( 'rates' ) ); ?>">
		<?php wp_nonce_field( 'awebooking_update_price' ); ?>

		<div class="awebooking-dialog-contents" style="padding: 1em;">
			<div id="js-scheduler-form-controls"></div>
		</div>

		<div class="awebooking-dialog-buttons">
			<button type="button" class="button"><?php echo esc_html__( 'Cancel', 'awebooking' ); ?></button>
			<button type="submit" class="button button-primary"><?php echo esc_html__( 'Submit', 'awebooking' ); ?></button>
		</div>
	</form>
</div>

<script type="text/template" id="tmpl-scheduler-pricing-controls">
	<input type="hidden" name="action" value="{{ data.action }}">
	<input type="hidden" name="calendar" value="{{ data.calendar }}">
	<input type="hidden" name="start_date" value="{{ data.startDate.format('YYYY-MM-DD') }}">
	<input type="hidden" name="end_date" value="{{ data.endDate.format('YYYY-MM-DD') }}">

	<p>{{ data.startDate }}</p>
</script>

<script>
(function($) {
	'use strict';

	$(function() {
		var awebooking = window.TheAweBooking;

		// Create the scheduler.
		var scheduler = new awebooking.ScheduleCalendar({
			el: '.awebooking-scheduler',
		});

		var compileHtmlControls = function(action) {
			var template = wp.template('scheduler-pricing-controls');
			var data = scheduler.model.toJSON();

			data.action = action;

			$('#js-scheduler-form-controls').html(template(data));
		};

		// Setup datepicker.
		var flatpickr = awebooking.Flatpickr('.flatpickr', {
			altInput: true,
			altFormat: 'F j, Y',
			dateFormat: 'Y-m-d',
			position: 'below',
			onChange: function(dates, date) {
				console.log(date)
				window.location.href = "<?php echo esc_url_raw( add_query_arg( 'date', '', $current_route ) ); ?>=" + date;
			},
		});

		// Setup popup
		var $popup = awebooking.Popup.setup($('#scheduler-form-dialog')[0]);
		$popup.dialog('close');

		var $schedulerForm = $('#scheduler-form');

		// Do something when clear dates.
		scheduler.on('clear', function() {
			window.swal && swal.close();

			$popup.dialog('close');
		});

		scheduler.on('action:set-price', function(e, model) {
			window.swal && swal.close();

			compileHtmlControls();

			$popup.dialog('open');
		});

		scheduler.on('action:reset-price', function(e, model) {
			awebooking.confirm(function() {
				compileHtmlControls('reset_price');

				$schedulerForm.submit();

				model.clear();
			});
		});
	});

})(jQuery);
</script>
