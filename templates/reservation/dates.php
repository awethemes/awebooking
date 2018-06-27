<?php
/**
 * The template displaying the reservation stay.
 *
 * This template can be overridden by copying it to {yourtheme}/awebooking/reservation/dates.php.
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
if ( ! $res_request = $reservation->get_previous_request() ) {
	return;
}

list( $check_in, $check_out ) = [
	abrs_date( $res_request['check_in'] ),
	abrs_date( $res_request['check_out'] ),
];

// The hotel in current search.
$hotel = $reservation->get_hotel();

$should_show_arrival_time = (
	$hotel['check_in_time'] && $hotel['check_out_time']
	&& true === abrs_get_option( 'display_guest_arrival_time', true )
);

?>

<div class="reservation__section reservation__section--dates">
	<div class="reservation-dates">
		<div class="reservation-date reservation-date--checkin">
			<span class="reservation-date__title"><?php echo esc_html__( 'Check In', 'awebooking' ); ?></span>

			<div class="reservation-date__date">
				<span class="reservation-date__day" data-date-format="d"><?php echo esc_html( $check_in->date_i18n( 'd' ) ); ?></span>
				<span class="reservation-date__week" data-date-format="l"><?php echo esc_html( $check_in->date_i18n( 'l' ) ); ?></span>
				<span class="reservation-date__year" data-date-format="M Y"><?php echo esc_html( $check_in->date_i18n( 'M Y' ) ); ?></span>
			</div>

			<?php if ( $should_show_arrival_time ) : ?>
				<span class="reservation-date__time">
					<?php
					/* translators: %s check-in time */
					printf( esc_html_x( 'from %s', 'check-in time', 'awebooking' ), esc_html( $hotel->get( 'check_in_time' ) ) );
					?>
				</span>
			<?php endif; ?>
		</div><!-- /.reservation-date-->

		<div class="reservation-date__arrow">
			<i class="aficon aficon-arrow-forward"></i>
		</div>

		<div class="reservation-date reservation-date--checkout">
			<span class="reservation-date__title"><?php echo esc_html__( 'Check Out', 'awebooking' ); ?></span>

			<div class="reservation-date__date">
				<span class="reservation-date__day" data-date-format="d"><?php echo esc_html( $check_out->date_i18n( 'd' ) ); ?></span>
				<span class="reservation-date__week" data-date-format="l"><?php echo esc_html( $check_out->date_i18n( 'l' ) ); ?></span>
				<span class="reservation-date__year" data-date-format="M Y"><?php echo esc_html( $check_out->date_i18n( 'M Y' ) ); ?></span>
			</div>

			<?php if ( $should_show_arrival_time ) : ?>
				<span class="reservation-date__time">
					<?php
					/* translators: %s check-out time */
					printf( esc_html_x( 'until %s', 'check-out time', 'awebooking' ), esc_html( $hotel->get( 'check_out_time' ) ) );
					?>
				</span>
			<?php endif; ?>

		</div><!-- /.reservation-date-->
	</div><!-- /.reservation-dates -->
</div><!-- /.reservation__section -->
