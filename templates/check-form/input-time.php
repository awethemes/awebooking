<?php
/**
 * The Template for displaying check form.
 *
 * This template can be overridden by copying it to yourtheme/awebooking/check-form/input-time.php.
 *
 * @author 		awethemes
 * @package 	AweBooking/Templates
 * @version     3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$request = awebooking()->make( 'request' );

$check_in = $request->filled( 'check_in' ) ? awebooking_sanitize_standard_date( $request['check_in'] ) : '';
$check_out = $request->filled( 'check_out' ) ? awebooking_sanitize_standard_date( $request['check_out'] ) : '';

if ( isset( $reservation ) ) {
	// ...
}

$unique_id = uniqid();

?>
<div class="awebooking-field awebooking-arrival-field">
	<label for="start-date-placeholder-<?php echo esc_attr( $unique_id ); ?>"><?php esc_html_e( 'Arrival Date', 'awebooking' ); ?></label>

	<div class="awebooking-field-group">
		<i class="awebookingf awebookingf-calendar"></i>
		<input type="text" class="awebooking-datepicker awebooking-input awebooking-start-date" data-init="datepicker" data-alt-field="#start-date-<?php echo esc_attr( $unique_id ); ?>" data-date-format="<?php echo esc_attr( $date_format ); ?>" data-min-nights="1" placeholder="<?php esc_html_e( 'Arrival Date', 'awebooking' ); ?>" id="start-date-placeholder-<?php echo esc_attr( $unique_id ); ?>">
		<input type="hidden" id="start-date-<?php echo esc_attr( $unique_id ); ?>" name="check_in" value="<?php echo esc_attr( $check_in ); ?>">
	</div>
</div>

<div class="awebooking-field awebooking-departure-field">
	<label for="end-date-placeholder-<?php echo esc_attr( $unique_id ); ?>"><?php esc_html_e( 'Departure Date', 'awebooking' ); ?></label>

	<div class="awebooking-field-group">
		<i class="awebookingf awebookingf-calendar"></i>
		<input type="text" class="awebooking-datepicker awebooking-input awebooking-end-date" data-init="datepicker" data-alt-field="#end-date-<?php echo esc_attr( $unique_id ); ?>" data-date-format="<?php echo esc_attr( $date_format ); ?>" placeholder="<?php esc_html_e( 'Departure Date', 'awebooking' ); ?>" id="end-date-placeholder-<?php echo esc_attr( $unique_id ); ?>">
		<input type="hidden" id="end-date-<?php echo esc_attr( $unique_id ); ?>" name="check_out" value="<?php echo esc_attr( $check_out ); ?>">
	</div>
</div>
