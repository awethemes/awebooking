<?php

use AweBooking\Admin\Admin_Utils;

?>
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
	<input type="hidden" name="action" value="set_pricing">
	<input type="hidden" name="unit_id" value="{{ data.data_id }}">
	<input type="hidden" name="start_date" value="{{ data.startDay.format('YYYY-MM-DD') }}">
	<input type="hidden" name="end_date" value="{{ data.endDay.format('YYYY-MM-DD') }}">

	<h3>{{{ data.room_type }}} <small>{{ data.unit_name }}</small></h3>
	<p>{{{ data.comments }}}</p>

	<p>
		<label class="skeleton-input-group">
			<input type="number" class="cmb2-text-small" step="any" name="price" style="width: 100px; height: 31px;">
			<span class="skeleton-input-group__addon"><?php echo sprintf( esc_html__( '%s / night', 'awebooking' ), esc_html( awebooking( 'currency' )->get_symbol() ) ); ?></span>
		</label>
	</p>

	<# if ( data.getNights() > 4 ) { #>
		<p>
			<span><?php echo esc_html__( 'Apply only for', 'awebooking' ) ?></span>
			<span class="inline-weekday-checkbox"><?php Admin_Utils::prints_weekday_checkbox( [ 'id' => 'only_day_options' ] ); ?></span>
		</p>
	<# } #>
</script>
