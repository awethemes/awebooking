<div>

	<form method="GET" action="<?php echo esc_url( arbs_page_permalink( 'search_results' ) ); ?>" role="search">
		<input type="hidden" name="check-in">
		<input type="hidden" name="check-out">

		<div class="abrs-inline-datepicker">
			<div id="js-inline-dates"></div><!-- JS: Inline datepicker -->
		</div>

		<p style="margin-top: 15px;">
			<button type="submit" class="abrs-button"><?php echo esc_html_x( 'Search', 'search availability', 'awebooking' ); ?></button>
		</p>
	</form>

</div>
