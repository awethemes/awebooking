<?php

$form_classes = [];
$max_select = 10;

$hotels = abrs_list_hotels();

?><form method="GET" action="<?php echo esc_url( abrs_get_page_permalink( 'search_results' ) ); ?>" class="<?php echo esc_attr( abrs_html_class( $form_classes ) ); ?>" role="search">

	<?php if ( ! get_option( 'permalink_structure' ) ) : ?>
		<input type="hidden" name="p" value="<?php echo esc_attr( abrs_get_page_id( 'check_availability' ) ); ?>">
	<?php endif ?>

	<?php if ( abrs_running_on_multilanguage() ) : ?>
		<input type="hidden" name="lang" value="<?php echo esc_attr( awebooking( 'multilingual' )->get_current_language() ); ?>">
	<?php endif ?>

	<div class="searchbox searchbox--horizontal">
		<div class="searchbox__wrapper searchbox__wrapper-4">
			<?php if ( abrs_multiple_hotels() && count( $hotels ) > 1 ) : ?>
				<div class="searchbox__box searchbox__box--hotel">
					<label class="searchbox__label searchbox__label--hotel">
						<span><?php esc_html_e( 'Hotel', 'awebooking' ); ?></span>
						<select name="hotel" id="">
							<?php foreach ( $hotels as $hotel ) : ?>
								<option value="<?php echo esc_attr( $hotel->get_id() ); ?>"><?php echo esc_html( $hotel->get( 'name' ) ); ?></option>
							<?php endforeach; ?>
						</select>
					</label>
				</div>
			<?php endif ?>
			<div class="searchbox__box searchbox__box--datepicker">
				<div class="searchbox__box-content">
					<div class="searchbox__box-icon">
						<i class="afc afc-calendar-alt"></i>
					</div>
					<div class="searchbox__box-child">
						<div class="searchbox__box--checkin">
							<label class="searchbox__label searchbox__label--checkin">
								<span><?php esc_html_e( 'Check In', 'awebooking' ); ?></span>
								<input type="text" class="hotel-input hotel-input--checkin form-input-transparent" name="check_in" value="<?php echo esc_attr( $res_request['check_in'] ); ?>" placeholder="<?php echo esc_html__( 'Check In', 'awebooking' ); ?>" autocomplete="off" aria-haspopup="true">
							</label>
						</div>
					</div>
				</div>
				<div class="searchbox__box-content">
					<div class="searchbox__box-icon">
						<i class="afc afc-calendar-alt"></i>
					</div>
					<div class="searchbox__box-child">
						<div class="searchbox__box--checkout">
							<label class="searchbox__label searchbox__label--checkout">
								<span><?php esc_html_e( 'Check Out', 'awebooking' ); ?></span>
								<input type="text" class="hotel-input hotel-input--checkout form-input-transparent" name="check_out" value="<?php echo esc_attr( $res_request['check_out'] ); ?>" placeholder="<?php echo esc_html__( 'Check Out', 'awebooking' ); ?>" autocomplete="off" aria-haspopup="true">
							</label>
						</div>
					</div>
				</div>
			</div>

			<div class="searchbox__box searchbox__box--occupancy">
				<div class="searchbox__box-content">
					<div class="searchbox__box-icon">
						<i class="afc afc-male"></i>
					</div>
					<div class="searchbox__box-child">
						<label class="searchbox__label">
							<span>Customer</span>
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
								<!-- <select name="adults" class="">
									<?php for ( $i = 1; $i <= $max_select; $i++ ) : ?>
										<option value="<?php echo esc_attr( $i ); ?>"><?php echo esc_html( $i ); ?></option>
									<?php endfor; ?>
								</select> -->
								<!-- <span class="searchbox-spinner__output">1</span> -->
								<input type="text" name="adults" maxlength="12" value="1" title="" class="searchbox-spinner__input form-input-transparent" />
								<span class="searchbox-spinner__title"><?php esc_html_e( 'Adults', 'awebooking' ); ?></span>
								<span class="searchbox-spinner__decrement">-</span>
								<span class="searchbox-spinner__increment">+</span>
							</label>
						
							<label class="searchbox-spinner">
								<!-- <span class="searchbox-spinner__output">1</span> -->
								<input type="text" name="children" maxlength="12" value="1" title="" class="searchbox-spinner__input form-input-transparent" />
								<span class="searchbox-spinner__title"><?php esc_html_e( 'Children', 'awebooking' ); ?></span>
								<span class="searchbox-spinner__decrement">-</span>
								<span class="searchbox-spinner__increment">+</span>
							</label>
						
							<label class="searchbox-spinner">
								<!-- <span class="searchbox-spinner__output">1</span> -->
								<input type="text" name="infants" maxlength="12" value="0" title="" class="searchbox-spinner__input form-input-transparent" />
								<span class="searchbox-spinner__title"><?php esc_html_e( 'Infants', 'awebooking' ); ?></span>
								<span class="searchbox-spinner__decrement">-</span>
								<span class="searchbox-spinner__increment">+</span>
							</label>
						</div>
					</div>
				</div>
			</div>

			<div class="searchbox__box searchbox__box--button">
				<div class="searchbox__box-content">
					<button class="button button--search searchbox__submit"><?php esc_html_e( 'Query', 'awebooking' ); ?></button>
				</div>
			</div>

		</div>

		<div class="rangepicker-container">
			<input type="hidden" data-hotel="rangepicker" style="display: none;" />
		</div>

	</div>
</form>
<script>
	(function($) {
		$('html,body').on('click', function() {
			$('.searchbox__box-content .searchbox__popup').removeClass('searchbox__popup--active');
		});

		$('.searchbox__box-content').on('click', function(e) {
			e.stopPropagation();

			var self = $(this);

			$('.searchbox__box-content .searchbox__popup').removeClass('searchbox__popup--active');

			self.find('.searchbox__popup').toggleClass('searchbox__popup--active');

		});

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
