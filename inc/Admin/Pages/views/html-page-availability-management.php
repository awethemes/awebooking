<?php

use AweBooking\Factory;
use AweBooking\Admin\Admin_Utils;
use AweBooking\Support\Carbonate;
use AweBooking\Support\Period;
use AweBooking\Admin\Calendar\Availability_Calendar;

global $room_type;

if ( isset( $_GET['room_type'] ) && ! empty( $_GET['room_type'] ) ) {
	$room_type = Factory::get_room_type( $_GET['room_type'] );

	if ( ! $room_type->exists() ) {
		return;
	}

	$month = ( isset( $_GET['month'] ) && $_GET['month'] >= 1 && $_GET['month'] <= 12 ) ? (int) $_GET['month'] : absint( date( 'n' ) );
	$calendar = (new Availability_Calendar( $room_type, $this->year, $month ));
} else {
	return;
}

$current_link = admin_url( 'admin.php?page=awebooking-availability&room_type=' . $room_type->get_id() );
$current_link = add_query_arg( 'year', $this->year, $current_link );

$select_period = Period::createFromMonth( $this->year, $month );
$select_period = $select_period->moveStartDate( '- 1MONTH' );
$select_period = $select_period->moveEndDate( '+ 5MONTH' );
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

		<div class="" style="position: relative; float: right;">
			<button type="button" class="button drawer-toggle toggle-year" data-init="awebooking-toggle" aria-expanded="false">
				<?php echo Carbonate::createFromDate( $this->year, $month, 1 )->format( 'F Y' ); ?>
			</button>

			<ul class="split-button-body awebooking-main-toggle">
				<?php foreach ( $select_period->getDatePeriod( '1 MONTH' ) as $month ) :
					$month = Carbonate::create_date( $month ); ?>
					<li>
						<a href="<?php echo esc_url( add_query_arg( array( 'year' => $month->year, 'month' => $month->month ), $current_link ) ); ?>">
							<?php echo esc_html( $month->format( 'F Y' ) ); ?>
						</a>
					</li>
				<?php endforeach ?>
			</ul>
		</div>
	</div>

	<div style="padding-top: 10px;">
		<?php $calendar->display(); ?>
	</div>
</form>


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
	<input type="hidden" name="action" value="set_availability">
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
