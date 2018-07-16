<?php
/**
 * The template for displaying occupancy input in the search-form.php template
 *
 * This template can be overridden by copying it to {yourtheme}/awebooking/search-form/occupancy.php.
 *
 * @see      http://docs.awethemes.com/awebooking/developers/theme-developers/
 * @author   awethemes
 * @package  AweBooking
 * @version  3.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! $atts['occupancy'] ) {
	return;
}
?>

<div tabindex="0" class="searchbox__box searchbox__box--occupancy">
	<div class="searchbox__box-wrap">
		<div class="searchbox__box-icon">
			<i class="aficon aficon-men"></i>
		</div>

		<div class="searchbox__box-line">
			<label class="searchbox__box-label">
				<span><?php esc_html_e( 'Customer', 'awebooking' ); ?></span>
			</label>

			<div class="searchbox__box-input searchbox-occupancy-info">
				<span class="searchbox-occupancy-info__item">
					<span class="searchbox-occupancy-info__number" data-bind="text: adults"><?php echo esc_attr( $res_request['adults'] ); ?></span>
					<?php esc_html_e( 'adult(s)', 'awebooking' ); ?>
				</span>

				<?php if ( abrs_children_bookable() ) : ?>
					<span class="searchbox-occupancy-info__item">
						<span class="searchbox-occupancy-info__number" data-bind="text: children"><?php echo esc_attr( $res_request['children'] ); ?></span>
						<?php esc_html_e( 'children', 'awebooking' ); ?>
					</span>
				<?php endif; ?>

				<?php if ( abrs_infants_bookable() ) : ?>
					<span class="searchbox-occupancy-info__item">
						<span class="searchbox-occupancy-info__number" data-bind="text: infants"><?php echo esc_attr( $res_request['infants'] ); ?></span>
						<?php esc_html_e( 'infants', 'awebooking' ); ?>
					</span>
				<?php endif; ?>
			</div>

			<div class="searchbox__popup">
				<label class="searchbox-spinner">
					<input type="number" data-bind="value: adults" name="adults" maxlength="<?php echo absint( abrs_get_option( 'search_form_max_adults' ) ); ?>" value="<?php echo esc_attr( $res_request['adults'] ); ?>" class="searchbox-spinner__input form-input-transparent" />
					<span class=""><?php esc_html_e( 'Adults', 'awebooking' ); ?></span>
				</label>

				<?php if ( abrs_children_bookable() ) : ?>
					<label class="searchbox-spinner">
						<input type="number" data-bind="value: children" name="children" maxlength="<?php echo absint( abrs_get_option( 'search_form_max_children' ) ); ?>" value="<?php echo esc_attr( $res_request['children'] ); ?>" class="searchbox-spinner__input form-input-transparent" />
						<span class=""><?php esc_html_e( 'Children', 'awebooking' ); ?></span>
					</label>
				<?php endif; ?>

				<?php if ( abrs_infants_bookable() ) : ?>
					<label class="searchbox-spinner">
						<input type="number" data-bind="value: infants" name="infants" maxlength="<?php echo absint( abrs_get_option( 'search_form_max_infants' ) ); ?>" value="<?php echo esc_attr( $res_request['infants'] ); ?>"  class="searchbox-spinner__input form-input-transparent" />
						<span class=""><?php esc_html_e( 'Infants', 'awebooking' ); ?></span>
					</label>
				<?php endif; ?>
			</div>
		</div>
	</div>
</div>
