<style type="text/css">
	.drawer-toggle.toggle-year:before {
		color: #333;
		font-size: 16px;
		content: "\f145";
	}
	.form-type-checkbox {
		display: inline-block;
	}
	.wp-filter {
		z-index: 100;
	}
</style>

<div class="wrap">
	<h1><?php esc_html_e( 'Bulk Pricing Manager', 'awebooking' ); ?></h1>

	<form action="" method="POST">
		<div class="wp-filter" style="margin-bottom: 0;">
			<div style="float: left; margin: 10px 0;">
				<label>From</label>
				<input type="text" class="init-daterangepicker-start" name="datepicker-start" autocomplete="off" style="width: 100px;">

				<label>To</label>
				<input type="text" class="init-daterangepicker-end" name="datepicker-end" autocomplete="off" style="width: 100px;">

				<div id="edit-day-options" class="form-checkboxes" style="display: inline-block;">
					<div class="form-item form-type-checkbox">
					<input type="checkbox" id="edit-day-options-1" name="day_options[]" value="1" checked="checked" class="form-checkbox">  <label class="option" for="edit-day-options-1">Mon </label>
					</div>

					<div class="form-item form-type-checkbox">
					<input type="checkbox" id="edit-day-options-2" name="day_options[]" value="2" checked="checked" class="form-checkbox">  <label class="option" for="edit-day-options-2">Tue </label>
					</div>

					<div class="form-item form-type-checkbox">
					<input type="checkbox" id="edit-day-options-3" name="day_options[]" value="3" checked="checked" class="form-checkbox">  <label class="option" for="edit-day-options-3">Wed </label>
					</div>

					<div class="form-item form-type-checkbox">
					<input type="checkbox" id="edit-day-options-4" name="day_options[]" value="4" checked="checked" class="form-checkbox">  <label class="option" for="edit-day-options-4">Thu </label>
					</div>

					<div class="form-item form-type-checkbox">
					<input type="checkbox" id="edit-day-options-5" name="day_options[]" value="5" checked="checked" class="form-checkbox">  <label class="option" for="edit-day-options-5">Fri </label>
					</div>

					<div class="form-item form-type-checkbox">
					<input type="checkbox" id="edit-day-options-6" name="day_options[]" value="6" checked="checked" class="form-checkbox">  <label class="option" for="edit-day-options-6">Sat </label>
					</div>

					<div class="form-item form-type-checkbox">
					<input type="checkbox" id="edit-day-options-0" name="day_options[]" value="0" checked="checked" class="form-checkbox">  <label class="option" for="edit-day-options-0">Sun </label>
					</div>
				</div>

				<input type="number" name="bulk-price" style="width: 100px;">

				<input type="hidden" name="action" value="bulk-update">
				<button class="button" type="submit"><?php echo esc_html__( 'Bulk Update', 'awebooking' ) ?></button>
			</div>

			<div class="" style="position: relative; float: right;">
				<?php
				$screen = get_current_screen();
				$_year = $this->_year;
				$years = [ $_year - 1, $_year, $_year + 1 ];
				?>
				<button type="button" class="button drawer-toggle toggle-year" aria-expanded="false" data-init="awebooking-toggle"><?php echo $_year; ?></button>

				<ul class="split-button-body awebooking-main-toggle">
					<?php foreach ( $years as $year ) : ?>
						<li>
							<a href="<?php echo esc_url( admin_url( 'admin.php?page=manager-pricing&amp;=' . $screen->parent_base . '&year=' . $year ) ); ?>"><?php echo esc_html( $year ); ?></a>
						</li>
					<?php endforeach ?>
				</ul>
			</div>
		</div>

		<?php $this->display(); ?>
	</form>
</div><!-- //... -->

<div id="awebooking-set-price-popup" class="hidden" title="<?php echo esc_html__( 'Set Price', 'awebooking' ) ?>">
	<form action="" class="awebooking-form" method="POST">
		<div class="awebooking-dialog-contents" style="padding: 0 15px;">
			<!-- No contents here, we'll use ajax to handle dynamic HTML -->
		</div>

		<div class="awebooking-dialog-buttons">
			<button class="button button-primary" type="submit"><?php echo esc_html__( 'Save changes', 'awebooking' ) ?></button>
		</div>
	</form>
</div>

<script type="text/template" id="tmpl-pricing-calendar-form">
	<p>{{{ data.showComments() }}}</p>

	<input type="hidden" name="room_type" value="{{ data.data_id }}">
	<input type="hidden" name="start_date" value="{{ data.startDay.format('YYYY-MM-DD') }}">
	<input type="hidden" name="end_date" value="{{ data.endDay.format('YYYY-MM-DD') }}">

	<input type="hidden" name="action" value="set_pricing">

	<label><?php echo esc_html__( 'Price:', 'awebooking' ) ?></label>
	<input type="text" name="price">
	<span><?php echo awebooking( 'currency' )->get_symbol(); ?></span>
</script>
