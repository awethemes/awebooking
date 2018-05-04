<?php

$form_classes = [];

$max_select = 10;

?>

<form method="GET" action="<?php echo esc_url( abrs_get_page_permalink( 'search_results' ) ); ?>" class="<?php echo esc_attr( abrs_html_class( $form_classes ) ); ?>" role="search">

	<?php if ( ! get_option( 'permalink_structure' ) ) : ?>
		<input type="hidden" name="p" value="<?php echo esc_attr( abrs_get_page_id( 'check_availability' ) ); ?>">
	<?php endif ?>

	<?php if ( abrs_running_on_multilanguage() ) : ?>
		<input type="hidden" name="lang" value="<?php echo esc_attr( awebooking( 'multilingual' )->get_current_language() ); ?>">
	<?php endif ?>

	<div class="searchbox searchbox--horizontal">
		<div class="searchbox__wrapper">
			<?php if ( abrs_multiple_hotels() ) : ?>
				<div tabindex="0" class="searchbox__box searchbox__box--hotel">
					<label>
						<span><?php esc_html_e( 'Hotel', 'awebooking' ); ?></span>
						<input type="text" name="hotel">
					</label>
				</div>
			<?php endif ?>

			<div tabindex="0" class="searchbox__box searchbox__box--checkin">
				<label>
					<span><?php esc_html_e( 'Check In', 'awebooking' ); ?></span>
					<input type="text" name="check-in">
				</label>
			</div>

			<div tabindex="0" class="searchbox__box searchbox__box--checkout">
				<label>
					<span><?php esc_html_e( 'Check Out', 'awebooking' ); ?></span>
					<input type="text" name="check-out">
				</label>
			</div>

			<div tabindex="0" class="searchbox__box searchbox__box--occupancy">
				<label>
					<span><?php esc_html_e( 'Adults', 'awebooking' ); ?></span>
					<select name="adults">
						<?php for ( $i = 1; $i <= $max_select; $i++ ) : ?>
							<option value="<?php echo esc_attr( $i ); ?>"><?php echo esc_html( $i ); ?></option>
						<?php endfor; ?>
					</select>
				</label>

				<label>
					<span><?php esc_html_e( 'Children', 'awebooking' ); ?></span>

					<select name="children">
						<?php for ( $i = 1; $i <= $max_select; $i++ ) : ?>
							<option value="<?php echo esc_attr( $i ); ?>"><?php echo esc_html( $i ); ?></option>
						<?php endfor; ?>
					</select>
				</label>

				<label>
					<span><?php esc_html_e( 'Infants', 'awebooking' ); ?></span>

					<select name="infants">
						<?php for ( $i = 1; $i <= $max_select; $i++ ) : ?>
							<option value="<?php echo esc_attr( $i ); ?>"><?php echo esc_html( $i ); ?></option>
						<?php endfor; ?>
					</select>
				</label>
			</div>

			<button class="button button--search">
				<?php esc_html_e( 'Search', 'awebooking' ); ?>
			</button>

		</div>
	</div>

</form>
