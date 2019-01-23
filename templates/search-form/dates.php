<?php
/**
 * The template for displaying dates input in the search-form.php template
 *
 * This template can be overridden by copying it to {yourtheme}/awebooking/template-parts/search-form/dates.php.
 *
 * @see      http://docs.awethemes.com/awebooking/developers/theme-developers/
 * @author   awethemes
 * @package  AweBooking
 * @version  3.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

?>

<div class="searchbox__box searchbox__box--checkin">
	<div class="searchbox__box-wrap">
		<div class="searchbox__box-line">
			<label class="searchbox__box-label"><span><?php esc_html_e( 'Check In', 'awebooking' ); ?></span></label>

			<div class="searchbox__box-input">
				<input type="text" data-bind="value: checkInFormatted()" class="searchbox__input searchbox__input--checkin input-transparent" placeholder="" autocomplete="off" aria-haspopup="true">
				<input type="hidden" data-bind="value: checkInDate" name="check_in" value="<?php echo esc_attr( $res_request['check_in'] ); ?>">
			</div>
		</div>
	</div>
</div>

<div class="searchbox__box searchbox__box--checkout">
	<div class="searchbox__box-wrap">
		<div class="searchbox__box-line">
			<label class="searchbox__box-label"><span><?php esc_html_e( 'Check Out', 'awebooking' ); ?></span></label>

			<div class="searchbox__box-input">
				<input type="text" data-bind="value: checkOutFormatted()" class="searchbox__input searchbox__input--checkout input-transparent" placeholder="" autocomplete="off" aria-haspopup="true">
				<input type="hidden" data-bind="value: checkOutDate" name="check_out" value="<?php echo esc_attr( $res_request['check_out'] ); ?>">
			</div>
		</div>
	</div>
</div>
