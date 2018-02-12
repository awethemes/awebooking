<?php

use AweBooking\Support\Utils as U;

?><div class="awebooking-schedule">

	<div class="awebooking-schedule-head">
		<div class="awebooking-schedule__datepicker">

			<input type="text" name="" id="schedule-datepicker">
			<span id="schedule-datepicker-alt"></span>

		</div>
	</div>

	<aside class="awebooking-schedule__aside">
		<h2><?php echo esc_html( $scheduler->get_name() ); ?></h2>

		<ul>
		<?php foreach ( $scheduler as $calendar ) : ?>
			<li data-calendar="<?php echo esc_attr( $calendar->get_uid() ); ?>">
				<span><?php echo esc_html( $calendar->get_name() ); ?></span>
			</li>
		<?php endforeach ?>
		</ul>
	</aside><!-- /.awebooking-schedule__aside -->

	<div class="awebooking-schedule__table">

		<header class="awebooking-schedule__header">
			<div class="awebooking-schedule__row">

				<div class="awebooking-schedule__days">
				<?php foreach ( $period->get_date_period() as $date ) : ?>
					<div class="<?php echo esc_attr( implode( ' ', $this->get_date_classes( $date, 'awebooking-schedule__day-heading' ) ) ); ?>" title="<?php echo esc_attr( $date->format( $this->get_option( 'date_title' ) ) ); ?>" data-date="<?php echo esc_attr( $date->toDateString() ); ?>">
						<span class="weekday"><?php echo esc_html( $this->get_weekday_name( $date->dayOfWeek, 'abbrev' ) ); // @codingStandardsIgnoreLine ?></span>
						<span class="day"><?php echo esc_html( $date->format( 'd' ) ); ?></span>
					</div>
				<?php endforeach; ?>
				</div>

			</div>
		</header>

		<div class="awebooking-schedule__body">
			<?php foreach ( $scheduler as $calendar ) : // @codingStandardsIgnoreLine
				$events = U::rescue( function () use ( $calendar, $period ) {
					return $this->get_calendar_events( $calendar, $period );
				});
				?>

				<div class="awebooking-schedule__row" data-calendar="<?php echo esc_attr( $calendar->get_uid() ); ?>">

					<div class="awebooking-schedule__days">
						<?php foreach ( $period->get_date_period() as $date ) : ?>
							<div class="<?php echo esc_attr( implode( ' ', $this->get_date_classes( $date, 'awebooking-schedule__day' ) ) ); ?>" data-date="<?php echo esc_attr( $date->toDateString() ); ?>">
								<?php echo $this->get_cell_date_contents( $date, $calendar ); // WPCS: XSS OK. ?>
							</div>
						<?php endforeach; ?>
					</div>

					<?php if ( ! is_null( $events ) ) : ?>

						<div class="awebooking-schedule__events">
							<?php foreach ( $period->get_date_period() as $date ) : ?>
								<div class="awebooking-schedule__cell-event" data-date="<?php echo esc_attr( $date->toDateString() ); ?>">
									<?php echo $this->get_cell_event_contents( $events, $date, $calendar ); // WPCS: XSS OK. ?>
								</div>
							<?php endforeach ?>
						</div>

					<?php endif; ?>

				</div>
			<?php endforeach; ?>

			<div class="awebooking-schedule__marker" style="display: none;">
				<span class="awebooking-schedule__markerspan"></span>
			</div>
		</div><!-- /.awebooking-schedule__body -->

	</div><!-- /.awebooking-schedule__table -->

	<div class="popper awebooking-schedule_popper" style="display: none;">
		<div class="popper__arrow" x-arrow></div>
		<?php echo $this->generate_actions_menu(); // WPCS: XSS OK. ?>
	</div>

</div><!-- /.awebooking-schedule -->

<script type="text/javascript">
	jQuery(function($) {
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
</script>
