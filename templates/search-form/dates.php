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

<div tabindex="0" class="searchbox__box searchbox__box--checkin">
	<div class="searchbox__box-wrap">
		<div class="searchbox__box-icon">
			<i class="aficon aficon-calendar"></i>
		</div>

		<div class="searchbox__box-line">
			<label class="searchbox__box-label">
				<span><?php esc_html_e( 'Check In', 'awebooking' ); ?></span>
			</label>

			<div class="searchbox__box-input">
				<span class="searchbox__input-display" data-bind="text: checkInFormatted()"></span>
				<input type="hidden" data-bind="value: checkInDate" class="searchbox__input searchbox__input--checkin input-transparent" name="check_in" value="<?php echo esc_attr( $res_request['check_in'] ); ?>" placeholder="<?php esc_attr_e( 'Check In', 'awebooking' ); ?>" autocomplete="off" aria-haspopup="true">
			</div>
		</div>
	</div>
</div>

<div tabindex="0" class="searchbox__box searchbox__box--checkout">
	<div class="searchbox__box-wrap">
		<div class="searchbox__box-icon">
			<i class="aficon aficon-calendar"></i>
		</div>

		<div class="searchbox__box-line">
			<label class="searchbox__box-label">
				<span><?php esc_html_e( 'Check Out', 'awebooking' ); ?></span>
			</label>

			<div class="searchbox__box-input">
				<span class="searchbox__input-display" data-bind="text: checkOutFormatted()"></span>
				<input type="hidden" data-bind="value: checkOutDate" class="searchbox__input searchbox__input--checkout input-transparent" name="check_out" value="<?php echo esc_attr( $res_request['check_out'] ); ?>" placeholder="<?php esc_attr_e( 'Check Out', 'awebooking' ); ?>" autocomplete="off" aria-haspopup="true">
			</div>
		</div>
	</div>
</div>
