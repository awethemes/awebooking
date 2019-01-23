<?php
/**
 * The template for displaying occupancy input in the search-form.php template
 *
 * This template can be overridden by copying it to {yourtheme}/awebooking/search-form/occupancy.php.
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

<div class="abrs-searchbox__box abrs-searchbox__box--adults">
	<div class="abrs-searchbox__box-wrap">
		<label class="abrs-searchbox__box-label" for="<?php echo esc_attr( $search_form->id( 'adults' ) ); ?>">
			<?php esc_html_e( 'Adults', 'awebooking' ); ?>
		</label>

		<div class="abrs-searchbox__box-input">
			<?php $search_form->adults(); ?>
		</div>
	</div>
</div>

<?php if ( abrs_children_bookable() ) : ?>
	<div class="abrs-searchbox__box abrs-searchbox__box--children">
		<div class="abrs-searchbox__box-wrap">
			<label class="abrs-searchbox__box-label" for="<?php echo esc_attr( $search_form->id( 'children' ) ); ?>">
				<?php esc_html_e( 'Children', 'awebooking' ); ?>
			</label>

			<div class="abrs-searchbox__box-input">
				<?php $search_form->children(); ?>
			</div>
		</div>
	</div>
<?php endif; ?>

<?php if ( abrs_infants_bookable() ) : ?>
	<div class="abrs-searchbox__box abrs-searchbox__box--children">
		<div class="abrs-searchbox__box-wrap">
			<label class="abrs-searchbox__box-label" for="<?php echo esc_attr( $search_form->id( 'infants' ) ); ?>">
				<?php esc_html_e( 'Infants', 'awebooking' ); ?>
			</label>

			<div class="abrs-searchbox__box-input">
				<?php $search_form->infants(); ?>
			</div>
		</div>
	</div>
<?php endif; ?>
