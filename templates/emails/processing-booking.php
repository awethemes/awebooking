<?php
/**
 * Processing booking email.
 *
 * This template can be overridden by copying it to yourtheme/awebooking/emails/processing-booking.php.
 *
 * @author 		Awethemes
 * @package 	AweBooking/Templates
 * @version     3.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

abrs_mailer()->header( $email );

echo wp_kses_post( wpautop( wptexturize( $content ) ) );

abrs_mailer()->footer( $email );
