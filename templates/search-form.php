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
	'searchbox',
	$atts['layout'] ? 'searchbox--' . $atts['layout'] : '',
	$atts['alignment'] ? 'searchbox--align-' . $atts['alignment'] : '',
	$atts['container_class'] ? $atts['container_class'] : '',
];
?>
<form method="GET" action="<?php echo esc_url( abrs_get_page_permalink( 'search_results' ) ); ?>" role="search">

	<?php if ( ! get_option( 'permalink_structure' ) ) : ?>
		<input type="hidden" name="p" value="<?php echo esc_attr( abrs_get_page_id( 'check_availability' ) ); ?>">
	<?php endif ?>

	<?php if ( abrs_running_on_multilanguage() ) : ?>
		<input type="hidden" name="lang" value="<?php echo esc_attr( abrs_multilingual()->get_current_language() ); ?>">
	<?php endif ?>

	<div class="<?php echo esc_attr( abrs_html_class( $form_classes ) ); ?>">
		<div class="searchbox__wrap">

			<?php if ( abrs_multiple_hotels() && $atts['hotel_location'] ) : ?>
				<div tabindex="0" class="searchbox__box searchbox__box--hotel">
					<div class="searchbox__box-wrap">
						<div class="searchbox__box-icon">
							<i class="aficon aficon-search"></i>
						</div>

						<div class="searchbox__box-line">
							<label class="searchbox__box-label">
								<span><?php esc_html_e( 'Hotel', 'awebooking' ); ?></span>
								<select name="hotel" class="input-transparent">
									<?php foreach ( abrs_list_hotels() as $hotel ) : ?>
										<option value="<?php echo esc_attr( $hotel->get_id() ); ?>"><?php echo esc_html( $hotel->get( 'name' ) ); ?></option>
									<?php endforeach; ?>
								</select>
							</label>
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
							<input type="text" class="searchbox__input searchbox__input--checkin input-transparent" name="check_in" value="<?php echo esc_attr( $res_request['check_in'] ); ?>" placeholder="<?php echo esc_html__( 'Check In', 'awebooking' ); ?>" autocomplete="off" aria-haspopup="true">
						</label>
					</div>

					<div class="searchbox__popup">
						<div class="rangepicker-container">
							<input type="hidden" data-hotel="rangepicker" style="display: none;" />
						</div>
					</div><!-- /.searchbox__popup -->
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
							<input type="text" class="searchbox__input searchbox__input--checkout input-transparent" name="check_out" value="<?php echo esc_attr( $res_request['check_out'] ); ?>" placeholder="<?php echo esc_html__( 'Check Out', 'awebooking' ); ?>" autocomplete="off" aria-haspopup="true">
						</label>
					</div>
				</div>
			</div>

			<div tabindex="0" class="searchbox__box searchbox__box--occupancy">
				<div class="searchbox__box-wrap">
					<div class="searchbox__box-icon">
						<i class="aficon aficon-men"></i>
					</div>

					<div class="searchbox__box-line">
						<label class="searchbox__box-label">
							<span><?php esc_html_e( 'Customer', 'awebooking' ); ?></span>
							<div class="searchbox-occupancy-info">
								<span class="searchbox-occupancy-info__item">
									<span class="searchbox-occupancy-info__number">1</span>
									<?php esc_html_e( 'Adults', 'awebooking' ); ?>
								</span>
								<span class="searchbox-occupancy-info__item">
									<span class="searchbox-occupancy-info__number">1</span>
									<?php esc_html_e( 'Children', 'awebooking' ); ?>
								</span>
								<span class="searchbox-occupancy-info__item">
									<span class="searchbox-occupancy-info__number">0</span>
									<?php esc_html_e( 'Infants', 'awebooking' ); ?>
								</span>
							</div>
						</label>

						<div class="searchbox__popup">
							<label class="searchbox-spinner">
								<input type="text" name="adults" maxlength="<?php echo absint( abrs_get_option( 'search_form_max_adults' ) ); ?>" value="1" title="" class="searchbox-spinner__input form-input-transparent" />
								<span class="searchbox-spinner__title"><?php esc_html_e( 'Adults', 'awebooking' ); ?></span>
								<span class="searchbox-spinner__decrement"><?php echo esc_html_x( '-', 'minus button', 'awebooking' ); ?></span>
								<span class="searchbox-spinner__increment"><?php echo esc_html_x( '+', 'plus button', 'awebooking' ); ?></span>
							</label>

							<?php if ( abrs_children_bookable() ) : ?>
								<label class="searchbox-spinner">
									<input type="text" name="children" maxlength="<?php echo absint( abrs_get_option( 'search_form_max_children' ) ); ?>" value="1" title="" class="searchbox-spinner__input form-input-transparent" />
									<span class="searchbox-spinner__title"><?php esc_html_e( 'Children', 'awebooking' ); ?></span>
									<span class="searchbox-spinner__decrement"><?php echo esc_html_x( '-', 'minus button', 'awebooking' ); ?></span>
									<span class="searchbox-spinner__increment"><?php echo esc_html_x( '+', 'plus button', 'awebooking' ); ?></span>
								</label>
							<?php endif; ?>

							<?php if ( abrs_infants_bookable() ) : ?>
								<label class="searchbox-spinner">
									<input type="text" name="infants" maxlength="<?php echo absint( abrs_get_option( 'search_form_max_infants' ) ); ?>" value="0" title="" class="searchbox-spinner__input form-input-transparent" />
									<span class="searchbox-spinner__title"><?php esc_html_e( 'Infants', 'awebooking' ); ?></span>
									<span class="searchbox-spinner__decrement"><?php echo esc_html_x( '-', 'minus button', 'awebooking' ); ?></span>
									<span class="searchbox-spinner__increment"><?php echo esc_html_x( '+', 'plus button', 'awebooking' ); ?></span>
								</label>
							<?php endif; ?>
						</div>
					</div>
				</div>
			</div>

			<div tabindex="0" class="searchbox__box searchbox__box--button">
				<div class="searchbox__box-wrap">
					<button class="button button--search searchbox__submit"><?php esc_html_e( 'Search', 'awebooking' ); ?></button>
				</div>
			</div>

		</div><!-- /.searchbox__wrapper-->
	</div><!-- /.searchbox-->


</form>
<script>
	(function($) {
		$('.searchbox-spinner').each(function() {
			var self = $(this),
				decrement = $('.searchbox-spinner__decrement', self),
				increment = $('.searchbox-spinner__increment', self),
				input = $('.searchbox-spinner__input', self),
				title = $('.searchbox-spinner__title', self),
				value = input.val(),
				index;

			increment.on('click', function() {
				index = self.index();
				value =  isNaN(value) ? 0 : value;
				value++;
				input.val(value);

				getNumber(index, value);

			});

			decrement.on('click', function() {
				index = self.index();
				value =  isNaN(value) ? 0 : value;
				value > 0 ? value-- : value;
				input.val(value);

				getNumber(index, value);
			});
		});

		function getNumber(index, value) {
			$('.searchbox-occupancy-info .searchbox-occupancy-info__item:eq('+index+')').find('.searchbox-occupancy-info__number').text(value);
		}

	})(jQuery);
</script>
