<?php

use AweBooking\Admin\Admin_Utils;

?>
<style type="text/css">
	.drawer-toggle.toggle-year:before {
		color: #333;
		font-size: 16px;
		content: "\f145";
	}
	.form-type-checkbox {
		display: inline-block;
	}
	.wp-filter {
		z-index: 100;
	}
</style>

<div class="wrap">
	<h1><?php esc_html_e( 'Bulk Pricing Manager', 'awebooking' ); ?></h1>

	<form action="" method="POST">
		<div class="wp-filter awebooking-toolbar-container">
			<div style="float: left;"">
				<input class="wp-toggle-checkboxes" type="checkbox">
				<span class="awebooking-sperator"> | </span>

				<label><?php esc_html_e( 'From', 'awebooking' ); ?></label>
				<input type="text" class="init-daterangepicker-start" name="datepicker-start" autocomplete="off" style="width: 100px;">

				<label><?php esc_html_e( 'To', 'awebooking' ); ?></label>
				<input type="text" class="init-daterangepicker-end" name="datepicker-end" autocomplete="off" style="width: 100px;">

				<div id="edit-day-options" class="inline-weekday-checkbox">
					<?php Admin_Utils::prints_weekday_checkbox( [ 'id' => 'day_options' ] ); ?>
				</div>

				<input type="number" step="any" name="bulk-price" style="width: 100px;">

				<input type="hidden" name="action" value="bulk-update">
				<button class="button" type="submit"><?php echo esc_html__( 'Bulk Update', 'awebooking' ); ?></button>
			</div>

			<div class="" style="position: relative; float: right;">
				<?php
				$screen = get_current_screen();
				$_year = $this->_year;
				$years = [ $_year - 1, $_year, $_year + 1 ];
				?>
				<button type="button" class="button drawer-toggle toggle-year" aria-expanded="false" data-init="awebooking-toggle"><?php echo $_year; ?></button>

				<ul class="split-button-body awebooking-main-toggle">
					<?php foreach ( $years as $year ) : ?>
						<li>
							<a href="<?php echo esc_url( admin_url( 'admin.php?page=manager-pricing&amp;=' . $screen->parent_base . '&year=' . $year ) ); ?>"><?php echo esc_html( $year ); ?></a>
						</li>
					<?php endforeach ?>
				</ul>
			</div>
		</div>

		<?php
		$this->display_tablenav( 'top' );

		$this->screen->render_screen_reader_content( 'heading_list' );
		?><table class="wp-list-table <?php echo implode( ' ', $this->get_table_classes() ); ?>">

			<tbody id="the-list">
				<?php $this->display_rows_or_placeholder(); ?>
			</tbody>
		</table>
		<?php $this->display_tablenav( 'bottom' ); ?>

	</form>
</div><!-- //... -->

<?php include trailingslashit( __DIR__ ) . 'template-pricing-management.php'; ?>
