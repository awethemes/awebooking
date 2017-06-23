<?php
/**
 * The Template for displaying check form.
 *
 * This template can be overridden by copying it to yourtheme/awebooking/check-form/input-time.php.
 *
 * @author 		awethemes
 * @package 	AweBooking/Templates
 * @version     1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

?>
<div class="awebooking-field">
	<label for=""><?php esc_html_e( 'Arrival Date', 'awebooking' ); ?></label>
	<div class="awebooking-field-group">
		<i class="awebookingf awebookingf-calendar"></i>
		<input type="text" class="awebooking-datepicker awebooking-input awebooking-start-date" data-init="datepicker" data-alt-field="#start-date" data-date-format="<?php echo esc_attr( $date_format ); ?>" data-min-nights="1" placeholder="<?php esc_html_e( 'Arrival Date', 'awebooking' ); ?>">
		<input type="hidden" id="start-date" name="start-date" value="<?php echo isset( $_GET['start-date'] ) ? $_GET['start-date'] : ''; ?>" />
	</div>
</div>

<div class="awebooking-field">
	<label for=""><?php esc_html_e( 'Departure Date', 'awebooking' ); ?></label>
	<div class="awebooking-field-group">
		<i class="awebookingf awebookingf-calendar"></i>
		<input type="text" class="awebooking-datepicker awebooking-input awebooking-end-date" data-init="datepicker" data-alt-field="#end-date" data-date-format="<?php echo esc_attr( $date_format ); ?>" placeholder="<?php esc_html_e( 'Departure Date', 'awebooking' ); ?>">
		<input type="hidden" id="end-date" name="end-date" value="<?php echo isset( $_GET['end-date'] ) ? $_GET['end-date'] : ''; ?>" />
	</div>
</div>
