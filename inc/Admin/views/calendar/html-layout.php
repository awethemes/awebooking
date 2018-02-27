<div class="awebooking-schedule">

	<div class="awebooking-schedule__head">
		<div class="awebooking-schedule__datepicker">

			<input type="text" name="" id="schedule-datepicker">
			<span id="schedule-datepicker-alt"></span>

		</div>
	</div>

	<aside class="awebooking-schedule__aside">
		<!-- <h2><?php echo esc_html( $scheduler->get_name() ); ?></h2> -->

		<ul class="awebooking-schedule__menu">
		<?php foreach ( $scheduler as $_scheduler ) : ?>
			<li>
				<div class="awebooking-schedule__menu-item">
					<strong class="togglename" data-target="<?php echo esc_attr( $_scheduler->get_reference()->get_id() ); ?>"><?php echo esc_html( $_scheduler->get_name() ); ?></strong>
				</div>

				<ul class="awebooking-schedule__submenu" data-schedule="<?php echo esc_attr( $_scheduler->get_reference()->get_id() ); ?>">
				<?php foreach ( $_scheduler as $calendar ) : ?>

					<li class="awebooking-schedule__menu-item" data-calendar="<?php echo esc_attr( $calendar->get_uid() ); ?>">
						<span><?php echo esc_html( $calendar->get_name() ); ?></span>
					</li>

				<?php endforeach ?>
				</ul>
			</li>
		<?php endforeach ?>
		</ul>
	</aside><!-- /.awebooking-schedule__aside -->

	<div class="awebooking-schedule__table">

		<header class="awebooking-schedule__header">
			<div class="awebooking-schedule__row">

				<div class="awebooking-schedule__days">
				<?php foreach ( $period as $date ) : ?>
					<div class="awebooking-schedule__column <?php echo esc_attr( implode( ' ', $cal->get_date_classes( $date, 'awebooking-schedule__day-heading' ) ) ); ?>" title="<?php echo esc_attr( $date->format( $cal->get_option( 'date_title' ) ) ); ?>" data-date="<?php echo esc_attr( $date->format( 'Y-m-d' ) ); ?>">
						<span class="weekday"><?php echo esc_html( $cal->get_weekday_name( $date->dayOfWeek, 'abbrev' ) ); // @codingStandardsIgnoreLine ?></span>
						<span class="day"><?php echo esc_html( $date->format( 'd' ) ); ?></span>

						<?php if ( 1 == $date->day ) : ?>
							<span class="newmonth"><?php echo esc_html( $date->format( 'Y-m' ) ); ?></span>
						<?php endif ?>
					</div>
				<?php endforeach; ?>
				</div>

			</div>
		</header>

		<div class="awebooking-schedule__body">
			<?php foreach ( $scheduler as $_scheduler ) : ?>
				<?php $this->partial( 'calendar/html-divider-row.php', compact( 'cal', '_scheduler', 'period' ) ); ?>

				<section class="awebooking-schedule__group" data-schedule="<?php echo esc_attr( $_scheduler->get_reference()->get_id() ); ?>">
					<?php foreach ( $_scheduler as $calendar ) : ?>

						<?php $this->partial( 'calendar/html-row.php', compact( 'cal', 'calendar', 'period' ) ); ?>

					<?php endforeach ?>
				</section>
			<?php endforeach ?>

			<div class="awebooking-schedule__marker" style="display: none;">
				<span class="awebooking-schedule__markerspan"></span>
			</div>
		</div><!-- /.awebooking-schedule__body -->

	</div><!-- /.awebooking-schedule__table -->

	<div class="popper awebooking-schedule_popper" style="display: none;">
		<div class="popper__arrow" x-arrow></div>
		<?php echo $actions_menu; // WPCS: XSS OK. ?>
	</div>

</div><!-- /.awebooking-schedule -->

<script type="text/javascript">
	jQuery(function($) {
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
</script>
