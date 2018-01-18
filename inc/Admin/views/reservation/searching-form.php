<?php

$request = awebooking()->make( 'request' );

// Prepare validate error messages.
$form->prepare_validate();

// Filled the inputs from request.
if ( $request->has( 'check_in_out' ) ) {
	$form['check_in_out']->set_value( $request->get( 'check_in_out' ) );
}

?><form class="awebooking-reservation__searching-from" method="GET" action="<?php echo esc_url( awebooking( 'url' )->admin_route( '/reservation/create' ) ); ?>">
	<input type="hidden" name="awebooking" value="/reservation/create">
	<input type="hidden" name="step" value="searching">

	<div class="awebooking-row">
		<div class="awebooking-column reservation_source_column">
			<?php print $form['reservation_source']->label(); // @codingStandardsIgnoreLine ?>
			<?php $form['reservation_source']->render(); ?>

			<?php $form['reservation_source']->errors(); ?>
		</div>

		<div class="awebooking-column check_in_out_column">
			<?php print $form['check_in_out']->label(); // @codingStandardsIgnoreLine ?>
			<?php $form['check_in_out']->render(); ?>

			<?php $form['check_in_out']->errors(); ?>
		</div>

		<div class="awebooking-column submit_column">
			<label>&nbsp;</label>

			<button class="button">
				<span class="dashicons dashicons-search"></span>
				<?php echo esc_html_x( 'Search', 'search availabilitsy', 'awebooking' ); ?>
			</button>
		</div>
	</div>

</form><!-- /.awebooking-reservation__searching-from -->
