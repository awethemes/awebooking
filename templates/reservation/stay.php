<?php
/**
 * The template displaying the reservation stay.
 *
 * This template can be overridden by copying it to {yourtheme}/awebooking/reservation/stay.php.
 *
 * @see      http://docs.awethemes.com/awebooking/developers/theme-developers/
 * @author   awethemes
 * @package  AweBooking
 * @version  3.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! $res_request = abrs_reservation()->resolve_res_request() ) {
	return;
}

list( $check_in, $check_out ) = [
	abrs_date( $res_request['check_in'] ),
	abrs_date( $res_request['check_out'] ),
];

?>

<div class="reservation-dates">
	<div class="reservation-date">
		<span class="reservation-date__subtitle"><?php echo esc_html__( 'Check-in', 'awebooking' ); ?></span>

		<div class="reservation-date__info">
			<span class="reservation-date__day" data-date-format="d"><?php echo esc_html( $check_in->date_i18n( 'd' ) ); ?></span>
			<span class="reservation-date__week" data-date-format="l"><?php echo esc_html( $check_in->date_i18n( 'l' ) ); ?></span>
			<span class="reservation-date__year" data-date-format="M Y"><?php echo esc_html( $check_in->date_i18n( 'M Y' ) ); ?></span>
		</div>

		<div class="reservation-date__from">
			<span class="reservation-date__time">
				<?php
					/* translators: %s check-in time */
					printf( esc_html_x( 'from %s', 'check-in time', 'awebooking' ), esc_html( abrs_get_hotel_check_time( 0 ) ) );
				?>
			</span>
		</div>
	</div>

	<div class="reservation-date">
		<div class="reservation-date__arrow">
			<i class="aficon aficon-arrow-forward"></i>
		</div>
	</div>

	<div class="reservation-date">
		<span class="reservation-date__subtitle"><?php echo esc_html__( 'Check-out', 'awebooking' ); ?></span>

		<div class="reservation-date__info">
			<span class="reservation-date__day" data-date-format="d"><?php echo esc_html( $check_out->date_i18n( 'd' ) ); ?></span>
			<span class="reservation-date__week" data-date-format="l"><?php echo esc_html( $check_out->date_i18n( 'l' ) ); ?></span>
			<span class="reservation-date__year" data-date-format="M Y"><?php echo esc_html( $check_out->date_i18n( 'M Y' ) ); ?></span>
		</div>

		<div class="reservation-date__from">
			<span class="reservation-date__time">
				<?php
					/* translators: %s check-out time */
					printf( esc_html_x( 'from %s', 'check-out time', 'awebooking' ), esc_html( abrs_get_hotel_check_time( 0, 'hotel_check_out' ) ) );
				?>
			</span>
		</div>
	</div>
</div>
