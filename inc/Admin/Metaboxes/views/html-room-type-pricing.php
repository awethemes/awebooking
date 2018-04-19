<div class="awebooking-panel abwrap" id="room-type-pricing" style="display: none;">
	<div class="abrow">
		<div class="abcol-3 abcol-sm-12">
			<?php $form->show_field( 'base_price' ); ?>
		</div>
	</div>

	<div class="abrow">
		<div class="abcol-10 abcol-sm-12" style="border-right: none;">
			<?php $form->show_field( '_rate_inclusions' ); ?>
		</div>
	</div>

	<div class="abrow">
		<div class="abcol-10 abcol-sm-12" style="border-right: none;">
			<?php $form->show_field( '_rate_policies' ); ?>
		</div>
	</div>

	<div class="abrow abrs-postbox-title">
		<div class="abcol">
			<h3><?php esc_html_e( 'Restrictions', 'awebooking' ); ?></h3>
			<p><?php esc_html_e( 'Limit the availability of the rate.', 'awebooking' ); ?></p>
		</div>
	</div>

	<div class="abrow">
		<div class="abcol-3 abcol-sm-12">
			<?php $form->show_field( 'minimum_night' ); ?>
		</div>

		<div class="abcol-3 abcol-sm-12">
			<?php $form->show_field( '_rate_maximum_los' ); ?>
		</div>
	</div>
</div><!-- /.awebooking-panel -->
