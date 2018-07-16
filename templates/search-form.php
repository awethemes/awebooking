<?php
/**
 * Display the search form.
 *
 * This template can be overridden by copying it to {yourtheme}/awebooking/search-form.php.
 *
 * @see      http://docs.awethemes.com/awebooking/developers/theme-developers/
 * @author   awethemes
 * @package  AweBooking
 * @version  3.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$form_classes = [
	$atts['layout'] ? 'searchbox--' . $atts['layout'] : '',
	$atts['alignment'] ? 'searchbox--align-' . $atts['alignment'] : '',
	$atts['container_class'] ? $atts['container_class'] : '',
];

$current_hotel = abrs_http_request()->get( 'hotel' );
if ( ! empty( $atts['only_room'] ) && is_numeric( $atts['only_room'] ) ) {
	$current_hotel = abrs_optional( abrs_get_room_type( $atts['only_room'] ) )->get( 'hotel_id' );
}

?>

<form method="GET" action="<?php echo esc_url( abrs_get_page_permalink( 'search_results' ) ); ?>" role="search">
	<?php if ( ! get_option( 'permalink_structure' ) ) : ?>
		<input type="hidden" name="p" value="<?php echo esc_attr( abrs_get_page_id( 'check_availability' ) ); ?>">
	<?php endif ?>

	<?php if ( abrs_running_on_multilanguage() ) : ?>
		<input type="hidden" name="lang" value="<?php echo esc_attr( abrs_multilingual()->get_current_language() ); ?>">
	<?php endif ?>

	<?php if ( abrs_is_room_type() ) : ?>
		<input type="hidden" name="hotel" value="<?php echo esc_attr( abrs_get_room_type( get_the_ID() )->get( 'hotel_id' ) ); ?>">
	<?php endif; ?>

	<?php if ( ! empty( $atts['only_room'] ) ) : ?>
		<input type="hidden" name="only" value="<?php echo esc_attr( implode( ',', wp_parse_id_list( $atts['only_room'] ) ) ); ?>">
	<?php endif ?>

	<div class="searchbox <?php echo esc_attr( abrs_html_class( $form_classes ) ); ?>">
		<div class="searchbox__wrap">
			<input type="text" data-hotel="rangepicker" style="display: none;"/>

			<?php if ( abrs_multiple_hotels() && ! abrs_is_room_type() && $atts['hotel_location'] ) : ?>
				<div tabindex="0" class="searchbox__box searchbox__box--hotel">
					<div class="searchbox__box-wrap">
						<div class="searchbox__box-icon">
							<i class="aficon aficon-search"></i>
						</div>

						<div class="searchbox__box-line">
							<label class="searchbox__box-label">
								<span><?php esc_html_e( 'Hotel', 'awebooking' ); ?></span>
							</label>

							<div class="searchbox__box-input">
								<select name="hotel" class="searchbox__input searchbox__input--hotel input-transparent">
									<?php foreach ( abrs_list_hotels( [], true ) as $hotel ) : ?>
										<option value="<?php echo esc_attr( $hotel->get_id() ); ?>" <?php selected( $hotel->get_id(), $current_hotel ); ?>><?php echo esc_html( $hotel->get( 'name' ) ); ?></option>
									<?php endforeach; ?>
								</select>
							</div>
						</div>
					</div>
				</div>
			<?php endif; ?>

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
							<span class="searchbox__input-display" data-bind="text: checkInFormatted"></span>
							<input type="hidden" data-bind="value: checkInDate" class="searchbox__input searchbox__input--checkin input-transparent" name="check_in" value="<?php echo esc_attr( $res_request['check_in'] ); ?>" placeholder="<?php echo esc_html__( 'Check In', 'awebooking' ); ?>" autocomplete="off" aria-haspopup="true">
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
							<span class="searchbox__input-display" data-bind="text: checkOutFormatted"></span>
							<input type="hidden" data-bind="value: checkOutDate" class="searchbox__input searchbox__input--checkout input-transparent" name="check_out" value="<?php echo esc_attr( $res_request['check_out'] ); ?>" placeholder="<?php echo esc_html__( 'Check Out', 'awebooking' ); ?>" autocomplete="off" aria-haspopup="true">
						</div>
					</div>
				</div>
			</div>

			<?php if ( $atts['occupancy'] ) : ?>
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
			<?php endif; ?>

			<div tabindex="0" class="searchbox__box searchbox__box--button">
				<div class="searchbox__box-wrap">
					<button class="button button--search searchbox__submit"><?php esc_html_e( 'Search', 'awebooking' ); ?></button>
				</div>
			</div>

		</div><!-- /.searchbox__wrapper-->
	</div><!-- /.searchbox-->
</form>
