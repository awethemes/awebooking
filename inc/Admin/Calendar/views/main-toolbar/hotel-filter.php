<?php
if ( ! abrs_multiple_hotels() ) {
	return;
}

?>

<div class="abrs-ptb1">
	<select name="hotel_id" id="hotel_id">
		<option value=""><?php esc_html_e( 'Filter by Hotel', 'awebooking' ) ?></option>
		<?php foreach ( abrs_list_hotels() as $hotel ) : ?>
			<option value="<?php echo esc_attr( $hotel->get_id() ); ?>" <?php selected( isset( $_GET['hotel'] ) ? absint( $_GET['hotel'] ) : 0, $hotel->get_id() ); ?>><?php echo esc_html( $hotel->get( 'name' ) ); ?></option>
		<?php endforeach; ?>
	</select>
</div>

<script>
	(function($, awebooking) {
		'use strict';

		$(function() {
			var plugin = window.awebooking || {};

			$('#hotel_id').on('change', function (e) {
				var hotel_id = this.value;

				setTimeout(function() {
					window.location.href = plugin.utils.addQueryArgs({ hotel: hotel_id });
				}, 500);
			});
		});

	})(jQuery);
</script>
