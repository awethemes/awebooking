<div class="awebooking-panel abwrap" id="room-type-general" style="display: block;">
	<div class="abrs-postbox-title"><h3><?php esc_html_e( 'Rooms', 'awebooking' ); ?></h3></div>

	<div class="abrow">
		<div class="abcol-6">
			<?php include trailingslashit( __DIR__ ) . 'html-room-type-rooms.php'; ?>
		</div>
	</div>

	<div class="abrs-postbox-title"><h3><?php esc_html_e( 'Occupancy', 'awebooking' ); ?></h3></div>

	<div class="abrow">
		<div class="abcol-3 abcol-sm-12">
			<?php $form->show_field( '_maximum_occupancy' ); ?>
		</div>

		<div class="abcol-3 abcol-sm-12">
			<?php $form->show_field( 'number_adults' ); ?>
		</div>

		<?php if ( abrs_children_bookable() ) : ?>
			<div class="abcol-3 abcol-sm-12">
				<?php $form->show_field( 'number_children' ); ?>
			</div>
		<?php endif ?>

		<?php if ( abrs_infants_bookable() ) : ?>
			<div class="abcol-3 abcol-sm-12" style="border-right: none;">
				<?php $form->show_field( 'number_infants' ); ?>
			</div>
		<?php endif ?>
	</div>

	<?php if ( abrs_infants_bookable() ) : ?>
		<div class="abrow">
			<div class="abcol">
				<?php $form->show_field( '_infants_in_calculations' ); ?>
			</div>
		</div>
	<?php endif ?>

	<div class="abrs-note abrs-mt1" style="max-width: 720px;">
		<h4><?php esc_html_e( 'Some notes on setting capacity', 'awebooking' ); ?></h4>
		<?php echo wp_kses_post( wpautop( __( "The number of adults, children etc. <b>do not</b> need to add up to the maximum occupancy. A room could sleep a maximum of 4 people, but the max adults may be 2 and max children 3. \n This would allow your guests to choose 2 adults and 2 children, or 1 adult and 3 children. (But never 2 adults and 3 children as this would exceed the max capacity.)", 'awebooking' ) ) ); ?>
	</div>
</div><!-- /#room-type-general -->
