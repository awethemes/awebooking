<div class="scheduler__header">
	<div class="scheduler__header-aside">
		<div class="scheduler__legends">
			<?php $calendar->perform_call_method( 'display_legends' ); ?>
		</div>
	</div><!-- /.scheduler__header-aside -->

	<div class="scheduler__header-toolbar">
		<div class="scheduler-flex">
			<?php $calendar->perform_call_method( 'display_toolbars' ); ?>
		</div>
	</div><!-- /.scheduler__header-toolbar -->
</div><!-- /.scheduler__header -->
