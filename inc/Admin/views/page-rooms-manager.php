<div class="wrap">
	<h1><?php esc_html_e( 'Availability Rooms Management', 'awebooking' ); ?></h1>

	<form method="post">
		<?php $this->display(); ?>
	</form>
</div>

<script type="text/javascript">
var BookingManager = {
	current: "<?php echo $this->current->firstOfMonth(); ?>",
}

jQuery(function($) {
	$('.init-daterangepicker-start').daterangepicker({
		showDropdowns: true,
		singleDatePicker: true,
		locale: { format: 'YYYY-MM-DD' }
	});

	$('.init-daterangepicker-end').daterangepicker({
		showDropdowns: true,
		singleDatePicker: true,
		locale: { format: 'YYYY-MM-DD' }
	});
});
</script>
