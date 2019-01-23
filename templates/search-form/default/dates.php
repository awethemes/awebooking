<?php
/**
 * The template for displaying dates input in the search-form.php template
 *
 * This template can be overridden by copying it to {yourtheme}/awebooking/template-parts/search-form/dates.php.
 *
 * @see      http://docs.awethemes.com/awebooking/developers/theme-developers/
 * @author   awethemes
 * @package  AweBooking
 * @version  3.2.0
 *
 * @var $search_form \AweBooking\Frontend\Search\Search_Form
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

?>

<div class="abrs-searchbox__box abrs-searchbox__box--checkin">
	<div class="abrs-searchbox__box-wrap">
		<label class="abrs-searchbox__box-label" for="<?php echo esc_attr( $search_form->id( 'check_in_alt' ) ); ?>">
			<?php esc_html_e( 'Check In', 'awebooking' ); ?>
		</label>

		<div class="abrs-searchbox__box-input">
			<?php $search_form->check_in(); ?>
		</div>
	</div>
</div>

<div class="abrs-searchbox__box abrs-searchbox__box--checkout">
	<div class="abrs-searchbox__box-wrap">
		<label class="abrs-searchbox__box-label" for="<?php echo esc_attr( $search_form->id( 'check_out_alt' ) ); ?>">
			<?php esc_html_e( 'Check Out', 'awebooking' ); ?>
		</label>

		<div class="abrs-searchbox__box-input">
			<?php $search_form->check_out(); ?>
		</div>
	</div>
</div>
