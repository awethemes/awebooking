<div>
<!-- 	<input type="text" id="check-in">
	<input type="text" id="check-out"> -->

	<form method="GET" action="<?php echo esc_url( arbs_get_page_permalink( 'search_results' ) ); ?>" role="search">
		<div id="select-dates"></div>
		<button type="submit" class="">Search</button>
	</form>

</div>

<script>
(function($) {

	$(function() {
		/*flatpickr('#check-in', {
			mode: 'range',
			showMonths: 2,
			plugins: [new rangePlugin({ input: "#check-out"})]
		});*/

		flatpickr("#select-dates", {
			mode: 'range',
			inline: true,
			showMonths: 2,
			// appendTo: null,
			minDate: 'today',
		});

	});

})(jQuery);
</script>
