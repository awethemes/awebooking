<?php
/**
 * The Template for checkout page.
 *
 * This template can be overridden by copying it to yourtheme/awebooking/checkout.php.
 *
 * @author 		Awethemes
 * @package 	Awethemes/Templates
 * @version     1.0.0
 */

use AweBooking\Support\Formatting;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
?>


<?php do_action( 'awebooking/template_notices' ); ?>

<?php if ( isset( $availability ) && $availability->available() ) : ?>
<table class="table table-hover">
	<thead>
		<tr>
			<th colspan="3"><?php printf( esc_html__( 'Rate: %s', 'awebooking' ), $room_type->get_title() ); ?></th>
		</tr>
		<tr>
			<th><?php esc_html_e( 'Room', 'awebooking' ); ?></th>
			<th><?php esc_html_e( 'Detail', 'awebooking' ); ?></th>
			<th><?php esc_html_e( 'Price', 'awebooking' ); ?></th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td>room-1</td>
			<td>June, 19 2017</td>
			<td><?php print $availability->get_price(); // WPCS: xss ok.?></td>
		</tr>
		<tr>
			<td class="text-right" colspan="2"><b><?php esc_html_e( 'Subtotal', 'awebooking' ); ?></b></td>
			<td><b><?php print $availability->get_price(); // WPCS: xss ok.?></b></td>
		</tr>
	</tbody>
</table>



<?php if ( $extra_services_name ) : ?>
<table class="table table-hover">
	<thead>
		<tr>
			<th colspan="3"><?php esc_html_e( 'Services', 'awebooking' ); ?></th>
		</tr>
		<tr>
			<th><?php esc_html_e( 'Service', 'awebooking' ); ?></th>
			<th><?php esc_html_e( 'Detail', 'awebooking' ); ?></th>
			<th><?php esc_html_e( 'Price', 'awebooking' ); ?></th>
		</tr>
	</thead>
	<tbody>
		<?php foreach ( $extra_services_infos as $key => $value ) : ?>
		<tr>
			<td><?php echo esc_html( $value['name'] ); ?></td>
			<td><?php echo esc_html( $value['label'] ); ?></td>
			<td><?php echo esc_html( $value['price_calculator'] ); ?></td>
		</tr>
		<?php endforeach; ?>
		<tr>
			<td class="text-right" colspan="2"><b><?php esc_html_e( 'Services Subtotal', 'awebooking' ); ?></b></td>
			<td><b><?php print $availability->get_extra_services_price(); // WPCS: xss ok.?></b></td>
		</tr>
		<tr>
			<td class="text-right" colspan="2"><b><?php esc_html_e( 'Total', 'awebooking' ); ?></b></td>
			<td><b><?php print $availability->get_total_price(); // WPCS: xss ok.?></b></td>
		</tr>
	</tbody>
</table>
<?php endif; ?>

<form id="awebooking-checkout-form" class="awebooking-checkout-form" method="POST">
	<?php wp_nonce_field( 'awebooking-checkout-nonce' ); ?>
	<input type="hidden" name="awebooking-action" value="checkout">

	<div class="awebooking-billing-fields">
		<h2 class="awebooking-checkout-form__title"><?php esc_html_e( 'Booking Details', 'awebooking' ); ?></h2>

		<div class="awebooking-field form-row-first">
			<label><?php esc_html_e( 'First Name', 'awebooking' ); ?> <abbr class="required" title="required">*</abbr></label>
			<input type="text" name="customer_first_name" class="awebooking-input" required="required">
		</div>

		<div class="awebooking-field form-row-last">
			<label><?php esc_html_e( 'Last Name', 'awebooking' ); ?> <abbr class="required" title="required">*</abbr></label>
			<input type="text" name="customer_last_name" class="awebooking-input" required="required">
		</div>

		<div class="awebooking-field form-row-first">
			<label><?php esc_html_e( 'Email Address', 'awebooking' ); ?> <abbr class="required" title="required">*</abbr></label>
			<input type="email" name="customer_email" class="awebooking-input" required="required">
		</div>

		<div class="awebooking-field form-row-last">
			<label><?php esc_html_e( 'Phone', 'awebooking' ); ?> <abbr class="required" title="required">*</abbr></label>
			<input type="text" name="customer_phone" class="awebooking-input" required="required">
		</div>

		<div class="awebooking-field">
			<label><?php esc_html_e( 'Company Name', 'awebooking' ); ?></label>
			<input type="text" name="customer_company" class="awebooking-input">
		</div>
	</div>

	<div class="awebooking-billing-fields awebooking-billing-fields--right">
		<h2 class="awebooking-checkout-form__title"><?php esc_html_e( 'Additional Information', 'awebooking' ); ?></h2>

		<div class="awebooking-field">
			<label><?php esc_html_e( 'Note', 'awebooking' ); ?></label>
			<textarea name="customer_note"></textarea>
		</div>

		<button type="submit" class="button" data-type="awebooking"><?php esc_html_e( 'Submit', 'awebooking' ); ?></button>
	</div>
</form>

<?php endif ?>
