<?php

use AweBooking\Admin\Admin_Utils;

?>
<style type="text/css">
	.drawer-toggle.toggle-year:before {
		color: #333;
		font-size: 16px;
		content: "\f145";
	}
	.form-item {
		display: inline-block;
	}
</style>

<div class="wrap">
	<h1><?php esc_html_e( 'Availability Management', 'awebooking' ); ?></h1>

	<form method="post">
		<div class="wp-filter" style="margin-bottom: 0; z-index: 100;">
			<div style="float: left; margin: 10px 0;">
				<label>From</label>
				<input type="text" class="init-daterangepicker-start" name="datepicker-start" autocomplete="off" style="width: 100px;">

				<label>To</label>
				<input type="text" class="init-daterangepicker-end" name="datepicker-end" autocomplete="off" style="width: 100px;">

				<div id="edit-day-options" class="form-checkboxes" style="display: inline-block;">
					<?php Admin_Utils::prints_weekday_checkbox( [ 'id' => 'day_options' ] ); ?>
				</div>

				<select name="state">
					<option value="0">Available</option>
					<option value="1">Unavailable</option>
				</select>

				<input type="hidden" name="action" value="bulk-update">
				<button class="button" type="submit"><?php echo esc_html__( 'Bulk Update', 'awebooking' ) ?></button>
			</div>

			<?php
			$screen = get_current_screen();

			$room_type = wp_data( 'posts', [
				'post_type' => 'room_type',
			]);

			$current_room_type = '';

			if ( isset( $_REQUEST['room-type'] ) && isset( $room_type[ $_REQUEST['room-type'] ] ) ) {
				$current_room_type = $room_type[ $_REQUEST['room-type'] ];
			} else {
				$current_room_type = 'All Room Types';
			}
			?>
			<div class="" style="position: relative; float: right;">
				<button type="button" class="button drawer-toggle" data-init="awebooking-toggle" aria-expanded="false"><?php echo $current_room_type; ?></button>

				<ul class="split-button-body awebooking-main-toggle">
					<li>
						<a href="<?php echo esc_url( admin_url( 'admin.php?page=manager-awebooking&amp;=' . $screen->parent_base ) ); ?>"><?php echo esc_html__( 'All Room Types', 'awebooking' ); ?></a>
					</li>

					<?php foreach ( $room_type as $id => $name ) : ?>
						<li>
							<a href="<?php echo esc_url( admin_url( 'admin.php?page=manager-awebooking&amp;=' . $screen->parent_base . '&amp;room-type=' . $id ) ); ?>"><?php echo esc_html( $name ); ?></a>
						</li>
					<?php endforeach ?>
				</ul>
			</div>

			<div class="" style="position: relative; float: right;">
				<?php
				$_year = $this->_year;
				$years = [ $_year - 1, $_year, $_year + 1 ];
				?>
				<button type="button" class="button drawer-toggle toggle-year" data-init="awebooking-toggle" aria-expanded="false"><?php echo $_year; ?></button>

				<ul class="split-button-body awebooking-main-toggle">
					<?php foreach ( $years as $year ) : ?>
						<li>
							<a href="<?php echo esc_url( admin_url( 'admin.php?page=manager-awebooking&amp;=' . $screen->parent_base . '&room-type=' . $this->room_type . '&year=' . $year ) ); ?>"><?php echo esc_html( $year ); ?></a>
						</li>
					<?php endforeach ?>
				</ul>
			</div>

		</div>

		<?php $this->display(); ?>
	</form>
</div>
