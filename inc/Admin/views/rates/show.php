<?php

use AweBooking\Admin\Forms\Set_Price_Form;

$set_price_form = new Set_Price_Form( $room_type );

?><div class="wrap">
	<h1 class="wp-heading-inline"><?php printf( esc_html__( '%s Pricing', 'awebooking' ), esc_html( $room_type->get_title() ) ); ?></h1>
	<hr class="wp-header-end">

	<?php $scheduler->display(); ?>
</div>

<div id="awebooking-add-line-item-popup" class="hidden">
	<form method="POST" action="<?php echo esc_attr( awebooking( 'url' )->admin_route( '/rates/663' ) ); ?>" class="awebooking-form">
		<div class="awebooking-form__loading"><span class="spinner"></span></div>

		<div class="awebooking-dialog-contents">
			<?php print $set_price_form->output(); // WPCS: XSS OK. ?>
		</div>

		<div class="awebooking-dialog-buttons">
			<button class="button button-primary" type="submit"><?php echo esc_html__( 'Add Room Unit', 'awebooking' ); ?></button>
		</div>

		<?php wp_referer_field(); ?>
	</form>
</div>

<script type="text/javascript">
	jQuery(function($) {
		new TheAweBooking.Popup( $('#awebooking-set-price')[0] );
	});
</script>
