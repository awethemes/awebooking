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
		<div class="wp-filter awebooking-toolbar-container" style="z-index: 100;">
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

				<select name="state">
					<option value="0"><?php esc_html_e( 'Available', 'awebooking' ); ?></option>
					<option value="1"><?php esc_html_e( 'Unavailable', 'awebooking' ); ?></option>
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
				$current_room_type = esc_html__( 'All Room Types', 'awebooking' );
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
</div>


<div id="awebooking-set-availability-popup" class="hidden" title="<?php echo esc_html__( 'Set availability', 'awebooking' ) ?>">
	<form action="" class="awebooking-form" method="POST">
		<div class="awebooking-form__loading"><span class="spinner"></span></div>

		<div class="awebooking-dialog-contents" style="padding: 0 15px;">
			<!-- No contents here, we'll use ajax to handle dynamic HTML -->
		</div>

		<div class="awebooking-dialog-buttons">
			<button class="button button-primary" type="submit"><?php echo esc_html__( 'Save changes', 'awebooking' ) ?></button>
		</div>
	</form>
</div>

<script type="text/template" id="tmpl-availability-calendar-form">
	<input type="hidden" name="room_id" value="{{ data.data_id }}">
	<input type="hidden" name="end_date" value="{{ data.endDay.format('YYYY-MM-DD') }}">
	<input type="hidden" name="start_date" value="{{ data.startDay.format('YYYY-MM-DD') }}">

	<h3>{{{ data.room_name }}}</h3>
	<p>{{{ data.comments }}}</p>

	<p>
		<label>
			<input type="radio" name="state" checked="" value="<?php echo esc_attr( AweBooking::STATE_AVAILABLE ); ?>">
			<?php echo esc_html__( 'Available', 'awebooking' ); ?>
		</label>

		<label>
			<input type="radio" name="state" value="<?php echo esc_attr( AweBooking::STATE_UNAVAILABLE ); ?>">
			<span><?php echo esc_html__( 'Unavailable', 'awebooking' ) ?></span>
		</label>
	</p>

	<# if ( data.getNights() > 4 ) { #>
		<p>
			<span><?php echo esc_html__( 'Apply only for', 'awebooking' ) ?></span>

			<span class="inline-weekday-checkbox">
				<?php Admin_Utils::prints_weekday_checkbox( [ 'id' => 'only_day_options' ] ); ?>
			</span>
		</p>
	<# } #>
</script>
