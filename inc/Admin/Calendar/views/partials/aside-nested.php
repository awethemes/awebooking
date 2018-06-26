<?php foreach ( $calendar->scheduler as $loop_scheduler ) : ?>
	<li>
		<div class="scheduler__menu">
			<strong class="scheduler__calendar-title">
				<i class="aficon aficon-bed"></i>
				<span><?php echo esc_html( $loop_scheduler->get_name() ); ?></span>
			</strong>

			<nav class="scheduler__nav-actions">
				<a href="<?php echo esc_url( get_edit_post_link( $loop_scheduler->get_uid() ) ); ?>" target="_blank">
					<span class="screen-reader-text"><?php esc_html_e( 'Show', 'awebooking' ); ?></span>
					<span class="dashicons dashicons-external"></span>
				</a>
			</nav>
		</div><!-- /.scheduler__menu -->

		<ul class="scheduler__submenu" data-schedule="<?php echo esc_attr( $loop_scheduler->get_reference()->get_id() ); ?>">
			<?php foreach ( $loop_scheduler as $loop_calendar ) : ?>
				<li class="scheduler__menu" data-calendar="<?php echo esc_attr( $loop_calendar->get_uid() ); ?>">
					<span><?php echo esc_html( $loop_calendar->get_name() ); ?></span>
				</li>
			<?php endforeach ?>
		</ul><!-- /.scheduler__submenu -->
	</li>
<?php endforeach ?>
