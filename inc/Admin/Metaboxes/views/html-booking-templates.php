<?php

use AweBooking\Factory;
use AweBooking\Admin\Forms\Add_Line_Item_Form;

global $post;
$the_booking = Factory::get_booking( $post->ID );

$add_item_form = new Add_Line_Item_Form( $the_booking );

?><div id="awebooking-add-line-item-popup" class="hidden" title="<?php echo esc_html__( 'Add New Room Unit', 'awebooking' ) ?>">
	<form action="post" class="awebooking-form" id="awebooking-add-line-item-form">
		<div class="awebooking-form__loading"><span class="spinner"></span></div>

		<div class="awebooking-dialog-contents">
			<?php $add_item_form->output(); ?>
		</div>

		<div class="awebooking-dialog-buttons">
			<button class="button button-primary" type="submit"><?php echo esc_html__( 'Add Room Unit', 'awebooking' ) ?></button>
		</div>
	</form>
</div>

<div id="awebooking-edit-line-item-popup" class="hidden" title="<?php echo esc_html__( 'Edit Room Unit', 'awebooking' ) ?>">
	<form action="post" class="awebooking-form">
		<!-- <div class="awebooking-form__loading"><span class="spinner"></span></div> -->

		<div class="awebooking-dialog-contents">
			<!-- No contents here, we'll use ajax to handle dynamic HTML -->
		</div>

		<div class="awebooking-dialog-buttons">
			<button class="button button-primary" type="submit"><?php echo esc_html__( 'Save changes', 'awebooking' ) ?></button>
		</div>
	</form>
</div>
