<?php
/* @vars $calendar \AweBooking\Admin\Calendar\Scheduler */

use AweBooking\Support\Utils as U;
use AweBooking\Admin\Calendar\Abstract_Scheduler;

if ( ! isset( $calendar ) || ! $calendar instanceof Abstract_Scheduler ) {
	return;
}

?><div class="scheduler nested-scheduler <?php echo esc_attr( $calendar->get_wrapper_classes() ); ?>">

	<?php $this->partial( 'scheduler/partials/header.php', compact( 'calendar' ) ); ?>

	<div class="scheduler__container">

		<aside class="scheduler__aside">
			<span class="scheduler__month-label"><?php echo esc_html( $calendar->get_period()->format( 'Y-m' ) ); ?></span>

			<div class="scheduler__aside-heading">
				<?php if ( $calendar_name = $calendar->get_scheduler()->get_name() ) : ?>
					<h2><?php echo esc_html( $calendar_name ); ?></h2>
				<?php endif ?>
			</div>

			<ul class="scheduler__menus">
				<?php $this->partial( 'scheduler/partials/aside-nested.php', compact( 'calendar' ) ); ?>
			</ul><!-- /.scheduler__menus -->

		</aside><!-- /.scheduler__aside -->

		<div class="scheduler__main">
			<header class="scheduler__heading">
				<?php $this->partial( 'scheduler/partials/row-heading.php', compact( 'calendar' ) ); ?>
			</header><!-- /.scheduler__heading -->

			<div class="scheduler__body">
				<?php foreach ( $calendar->get_scheduler() as $loop_scheduler ) : ?>
					<?php $this->partial( 'scheduler/partials/row-divider.php', compact( 'calendar', 'loop_scheduler' ) ); ?>

					<section class="scheduler__section" data-schedule="<?php echo esc_attr( U::optional( $loop_scheduler->get_reference() )->get_id() ); ?>">

						<?php foreach ( $loop_scheduler as $loop_calendar ) : ?>
							<div class="scheduler__row" data-calendar="<?php echo esc_attr( $loop_calendar->get_uid() ); ?>">
								<?php $this->partial( 'scheduler/partials/row-days.php', compact( 'calendar', 'loop_calendar' ) ); ?>

								<?php $this->partial( 'scheduler/partials/row-events.php', compact( 'calendar', 'loop_calendar' ) ); ?>
							</div><!-- /.scheduler__row -->
						<?php endforeach ?>

					</section><!-- /.scheduler__section -->
				<?php endforeach ?>

				<?php $this->partial( 'scheduler/partials/marker.php', compact( 'calendar' ) ); ?>

			</div><!-- /.scheduler__body -->
		</div><!-- /.scheduler__main -->

		<?php $this->partial( 'scheduler/partials/popper.php', compact( 'calendar' ) ); ?>

	</div><!-- /.scheduler__container -->
</div><!-- /.scheduler -->
