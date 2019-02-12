<?php
/**
 * The template for displaying occupancy input in the search-form.php template
 *
 * This template can be overridden by copying it to {yourtheme}/awebooking/search-form/occupancy.php.
 *
 * @see      http://docs.awethemes.com/awebooking/developers/theme-developers/
 * @author   awethemes
 * @package  AweBooking
 * @version  3.1.3
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! $atts['occupancy'] ) {
	return;
}
?>

<div class="searchbox__box searchbox__box--occupancy">
	<div class="searchbox__box-wrap">
		<div class="searchbox__box-line">
			<label class="searchbox__box-label">
				<span><?php esc_html_e( 'Customer', 'awebooking' ); ?></span>
			</label>

			<div class="searchbox__box-input searchbox-occupancy-info">
				<span class="searchbox-occupancy-info__item">
					<span class="searchbox-occupancy-info__number" data-bind="text: adults"><?php echo esc_html( $res_request['adults'] ); ?></span>
					<?php esc_html_e( 'adult(s)', 'awebooking' ); ?>
				</span>

				<?php if ( abrs_children_bookable() ) : ?>
					<span class="searchbox-occupancy-info__item">
						<span class="searchbox-occupancy-info__number" data-bind="text: children"><?php echo esc_html( $res_request['children'] ); ?></span>
						<?php esc_html_e( 'child(ren)', 'awebooking' ); ?>
					</span>
				<?php endif; ?>

				<?php if ( abrs_infants_bookable() ) : ?>
					<span class="searchbox-occupancy-info__item">
						<span class="searchbox-occupancy-info__number" data-bind="text: infants"><?php echo esc_html( $res_request['infants'] ); ?></span>
						<?php esc_html_e( 'infant(s)', 'awebooking' ); ?>
					</span>
				<?php endif; ?>
			</div>
		</div>
	</div>

	<div class="searchbox__popup">
		<div class="searchbox__popup-item searchbox__popup-item--adults">
			<label>
				<span><?php esc_html_e( 'Adults', 'awebooking' ); ?></span>
				<input type="number" name="adults" data-bind="value: adults" min="1" max="<?php echo absint( abrs_get_option( 'search_form_max_adults', 6 ) ); ?>" value="<?php echo esc_attr( $res_request['adults'] ); ?>" class="searchbox__input form-input-transparent" />
			</label>
		</div>

		<?php if ( abrs_children_bookable() ) : ?>
			<div class="searchbox__popup-item searchbox__popup-item--children">
				<label>
					<span><?php esc_html_e( 'Children', 'awebooking' ); ?></span>
					<input type="number" name="children" data-bind="value: children" min="0" max="<?php echo absint( abrs_get_option( 'search_form_max_children', 6 ) ); ?>" value="<?php echo esc_attr( $res_request['children'] ); ?>" class="searchbox__input form-input-transparent" />
				</label>
			</div>
		<?php endif; ?>

		<?php if ( abrs_infants_bookable() ) : ?>
			<div class="searchbox__popup-item searchbox__popup-item--infants">
				<label>
					<span><?php esc_html_e( 'Infants', 'awebooking' ); ?></span>
					<input type="number" data-bind="value: infants" name="infants" min="0" max="<?php echo absint( abrs_get_option( 'search_form_max_infants', 6 ) ); ?>" value="<?php echo esc_attr( $res_request['infants'] ); ?>"  class="searchbox__input form-input-transparent" />
				</label>
			</div>
		<?php endif; ?>
	</div>
</div>
