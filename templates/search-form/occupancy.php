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

<div tabindex="0" class="searchbox__box searchbox__box--occupancy">
	<div class="searchbox__box-wrap">
		<div class="searchbox__box-icon">
			<i class="aficon aficon-people"></i>
		</div>

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
		<div class="searchbox__spinner searchbox__spinner--adults">
			<div class="searchbox__spinner-box">
				<label class="searchbox__spinner-title"><?php esc_html_e( 'Adults', 'awebooking' ); ?></label>
				<div class="searchbox__spinner-wrap" data-trigger="spinner">
					<input type="number" data-spin="spinner" data-ruler="quantity" data-bind="value: adults" name="adults" data-min="1" data-max="<?php echo absint( abrs_get_option( 'search_form_max_adults' ) ); ?>" value="<?php echo esc_attr( $res_request['adults'] ); ?>" class="searchbox__spinner-input form-input-transparent" />
					<button type="button" class="searchbox__spinner-button searchbox__spinner-button--increment" data-spin="up"><?php echo esc_html_x( '+', 'plus button', 'awebooking' ); ?></button>
					<button type="button" class="searchbox__spinner-button searchbox__spinner-button--decrement" data-spin="down"><?php echo esc_html_x( '-', 'minus button', 'awebooking' ); ?></button>
				</div>
			</div>
		</div>

		<?php if ( abrs_children_bookable() ) : ?>
			<div class="searchbox__spinner searchbox__spinner--children">
				<div class="searchbox__spinner-box">
					<label class="searchbox__spinner-title"><?php esc_html_e( 'Children', 'awebooking' ); ?></label>
					<div class="searchbox__spinner-wrap" data-trigger="spinner">
						<input type="number" data-spin="spinner" data-ruler="quantity" data-bind="value: children" data-min="0" name="children" data-max="<?php echo absint( abrs_get_option( 'search_form_max_children' ) ); ?>" value="<?php echo esc_attr( $res_request['children'] ); ?>" class="searchbox__spinner-input form-input-transparent" />
						<button type="button" class="searchbox__spinner-button searchbox__spinner-button--increment" data-spin="up"><?php echo esc_html_x( '+', 'plus button', 'awebooking' ); ?></button>
						<button type="button" class="searchbox__spinner-button searchbox__spinner-button--decrement" data-spin="down"><?php echo esc_html_x( '-', 'minus button', 'awebooking' ); ?></button>
					</div>
				</div>
			</div>
		<?php endif; ?>

		<?php if ( abrs_infants_bookable() ) : ?>
			<div class="searchbox__spinner searchbox__spinner--infants">
				<div class="searchbox__spinner-box">
					<label class="searchbox__spinner-title"><?php esc_html_e( 'Infants', 'awebooking' ); ?></label>
					<div class="searchbox__spinner-wrap" data-trigger="spinner">
						<input type="number" data-spin="spinner" data-ruler="quantity" data-bind="value: infants" name="infants" data-min="0" data-max="<?php echo absint( abrs_get_option( 'search_form_max_infants' ) ); ?>" value="<?php echo esc_attr( $res_request['infants'] ); ?>"  class="searchbox__spinner-input form-input-transparent" />
						<button type="button" class="searchbox__spinner-button searchbox__spinner-button--increment" data-spin="up"><?php echo esc_html_x( '+', 'plus button', 'awebooking' ); ?></button>
						<button type="button" class="searchbox__spinner-button searchbox__spinner-button--decrement" data-spin="down"><?php echo esc_html_x( '-', 'minus button', 'awebooking' ); ?></button>
					</div>
				</div>
			</div>
		<?php endif; ?>
	</div>
</div>
