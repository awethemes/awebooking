<?php foreach ( $calendar->scheduler as $loop_calendar ) : ?>
	<li class="scheduler__menu">
		<strong class="scheduler__calendar-title">
			<i class="aficon aficon-bed"></i>
			<span><?php echo esc_html( $loop_calendar->get_name() ); ?></span>
		</strong>

		<nav class="scheduler__nav-actions">
			<?php $calendar->call( 'display_nav_actions' ); ?>

			<a href="<?php echo esc_url( get_edit_post_link( $loop_calendar->get_uid() ) ); ?>" target="_blank">
				<span class="screen-reader-text"><?php esc_html_e( 'Show', 'awebooking' ); ?></span>
				<span class="dashicons dashicons-external"></span>
			</a>
		</nav>
	</li><!-- /.scheduler__menu -->
<?php endforeach ?>
