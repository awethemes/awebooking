<?php
/**
 * The template displaying the reservation stay.
 *
 * This template can be overridden by copying it to {yourtheme}/awebooking/reservation/booked-single.php.
 *
 * @see      http://docs.awethemes.com/awebooking/developers/theme-developers/
 * @author   awethemes
 * @package  AweBooking
 * @version  3.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$reservation = abrs_reservation();
$res_request = $reservation->get_current_request();

if ( is_null( $res_request ) ) {
	$res_request = $reservation->get_previous_request();
}

if ( is_null( $res_request ) ) {
	return;
}

$check_in  = abrs_date( $res_request['check_in'] );
$check_out = abrs_date( $res_request['check_out'] );

?><div class="reservation_details">
	<div class="reservation_details___item tb-width-40">
		<span class="reservation_details__subtitle"><?php echo esc_html__( 'Check-in', 'awebooking' ); ?></span>

		<div class="reservation_details__info">
			<span class="meta-day" data-date-format="d"><?php echo esc_html( $check_in->date_i18n( 'd' ) ); ?></span>
			<span class="meta-day-week" data-date-format="l"><?php echo esc_html( $check_in->date_i18n( 'l' ) ); ?></span>
			<span class="meta-month-year" data-date-format="M Y"><?php echo esc_html( $check_in->date_i18n( 'M Y' ) ); ?></span>
		</div>

		<div class="reservation_details__from">
			<span class="meta-time">
				<?php
					/* translators: %s check-in time */
					printf( esc_html_x( 'from %s', 'check-in time', 'awebooking' ), esc_html( abrs_get_hotel_check_time( 0 ) ) );
				?>
			</span>
		</div>
	</div>

	<div class="reservation_details___item tb-width-20">
		<i class="fa ficon ficon-edge-arcolumn-right ficon-20"></i>
	</div>

	<div class="reservation_details___item tb-width-40">
		<span class="reservation_details__subtitle"><?php echo esc_html__( 'Check-out', 'awebooking' ); ?></span>

		<div class="reservation_details__info">
			<span class="meta-day" data-date-format="d"><?php echo esc_html( $check_out->date_i18n( 'd' ) ); ?></span>
			<span class="meta-day-week" data-date-format="l"><?php echo esc_html( $check_out->date_i18n( 'l' ) ); ?></span>
			<span class="meta-month-year" data-date-format="M Y"><?php echo esc_html( $check_out->date_i18n( 'M Y' ) ); ?></span>
		</div>

		<div class="reservation_details__from">
			<span class="meta-time">
				<?php
					/* translators: %s check-out time */
					printf( esc_html_x( 'from %s', 'check-out time', 'awebooking' ), esc_html( abrs_get_hotel_check_time( 0, 'hotel_check_out' ) ) );
				?>
			</span>
		</div>
	</div>
</div>
