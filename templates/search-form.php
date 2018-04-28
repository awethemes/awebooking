<?php

$form_classes = [];

?>

<form method="GET" action="<?php echo esc_url( abrs_page_permalink( 'search_results' ) ); ?>" class="<?php echo esc_attr( abrs_html_class( $form_classes ) ); ?>" role="search">

	<?php if ( ! get_option( 'permalink_structure' ) ) : ?>
		<input type="hidden" name="p" value="<?php echo esc_attr( abrs_get_page_id( 'check_availability' ) ); ?>">
	<?php endif ?>

	<?php if ( abrs_running_on_multilanguage() ) : ?>
		<input type="hidden" name="lang" value="<?php echo esc_attr( awebooking( 'multilingual' )->get_current_language() ); ?>">
	<?php endif ?>

	<div class="searchbox searchbox--horizontal">
		<div class="searchbox__wrapper">
			<div tabindex="0" class="searchbox__box searchbox__box--hotel">
				<input type="text" name="hotel" class="">
			</div>

			<div tabindex="0" class="searchbox__box searchbox__box--checkin">
				<input type="text" name="check-in" class="js-datepicker">
			</div>

			<div tabindex="0" class="searchbox__box searchbox__box--checkout">
				<input type="text" name="check-out" class="js-datepicker">
			</div>

			<div tabindex="0" class="searchbox__box searchbox__box--occupancy">
			</div>

			<button>
				<?php esc_html_e( 'Search', 'awebooking' ); ?>
			</button>

		</div>
	</div>

</form>

<script>
jQuery(function($) {

	awebooking.datepicker('.js-datepicker', {

	});

});
</script>
