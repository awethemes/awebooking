<?php
/**
 * Customer note email
 *
 * This template can be overridden by copying it to {yourtheme}/awebooking/emails/customer-note.php.
 *
 * @see      http://docs.awethemes.com/awebooking/developers/theme-developers/
 * @author   awethemes
 * @package  AweBooking
 * @version  3.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

abrs_mailer()->header( $email );

echo wp_kses_post( wpautop( wptexturize( $content ) ) );

abrs_mailer()->footer( $email );
