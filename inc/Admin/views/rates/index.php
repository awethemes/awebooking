<div class="wrap awebooking-wrap-rates">
	<h1 class="wp-heading-inline"><?php echo esc_html__( 'Pricing', 'awebooking' ); ?></h1>
	<hr class="wp-header-end">

	<div style="margin-top: 1em;"></div>
	<?php $scheduler->display(); ?>
</div><!-- /.wrap -->

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

	<h2>dasdasd</h2>

	<div class="booking-dates booking-dates--block">
		<div class="booking-dates__wrap">
			<div class="booking-dates__picker checkin">
				<label>Check In</label>
				<input type="text" name="start_date" value="{{ data.startDate.format('YYYY-MM-DD') }}" tabindex="-1">
			</div>

			<div class="booking-dates__nights">
				<span class=""></span>
				<span class=""></span>
			</div>

			<div class="booking-dates__picker checkout">
				<label>Check Out</label>
				<input type="text" name="end_date" value="{{ data.endDate.format('YYYY-MM-DD') }}" tabindex="-1">
			</div>
		</div>
	</div><!-- /.booking-dates -->

	<select name="adjust_operator">
		<option value="replace">Replace</option>
		<option value="add">Add</option>
		<option value="subtract">Subtract</option>
		<option value="multiply">Multiply</option>
		<option value="divide">Divide</option>
		<option value="increase">Increase</option>
		<option value="decrease">Decrease</option>
	</select>

	<input type="text" name="value" value="0" class="cmb2-text-small">

	<div>
		<?php foreach ( AweBooking\Dropdown::get_week_days( 'abbrev' ) as $key => $value) : ?>
			<label>
				<span><?php echo $value; ?></span>
				<input type="checkbox" name="checkbox" value="">
			</label>
		<?php endforeach; ?>
	</div>
</script>
