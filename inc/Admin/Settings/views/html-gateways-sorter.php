<ul class="abrs-sortable" id="js-sorting-gateways">
	<?php foreach ( awebooking( 'gateways' )->all() as $key => $gateway ) : ?>
		<li class="abrs-sortable__item">
			<input type="hidden" name="list_gateway_order[]" value="<?php echo esc_attr( $gateway->get_method() ); ?>">

			<div class="abrs-sortable__head">
				<span class="abrs-sortable__handle"></span>

				<span class="abrs-sortable__order">
					<?php if ( $gateway->is_enabled() ) : ?>
						<span class="abrs-state-indicator abrs-state-indicator--on" title="<?php esc_html_e( 'Enabled', 'awebooking' ); ?>"></span>
					<?php else : ?>
						<span class="abrs-state-indicator" title="<?php esc_html_e( 'Disabled', 'awebooking' ); ?>"></span>
					<?php endif ?>
				</span>
			</div>

			<div class="abrs-sortable__body">
				<strong><?php echo esc_html( $gateway->get_method_title() ); ?></strong>
			</div>
		</li>
	<?php endforeach ?>
</ul><!-- /.abrs-sortable -->

<script type="text/javascript">
(function($) {
	'use strict';

	$(function() {
		Sortable.create($('#js-sorting-gateways')[0], {
			handle: '.abrs-sortable__handle',
			animation: 150,
		});
	});

})(jQuery);
</script>
