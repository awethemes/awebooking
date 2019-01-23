<?php
/**
 * The template for displaying hotel input in the search-form.php template
 *
 * This template can be overridden by copying it to {yourtheme}/awebooking/template-parts/search-form/hotel.php.
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

if ( ! abrs_multiple_hotels() || abrs_is_room_type() ) {
	return;
}

?>

<div class="abrs-searchbox__box abrs-searchbox__box--hotel">
	<div class="abrs-searchbox__box-wrap">
		<label class="abrs-searchbox__box-label" for="<?php echo esc_attr( $search_form->id( 'hotel' ) ); ?>">
			<?php esc_html_e( 'Hotel', 'awebooking' ); ?>
		</label>

		<div class="abrs-searchbox__box-input">
			<?php $search_form->hotels(); ?>
		</div>
	</div>
</div>
