<?php
/**
 * Customer note email
 *
 * This template can be overridden by copying it to {yourtheme}/awebooking/emails/customer-note.php.
 *
 * HOWEVER, on occasion AweBooking will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see      http://docs.awethemes.com/awebooking/developers/theme-developers/
 * @author   awethemes
 * @package  AweBooking
 * @version  3.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/* @layout main.php */

$mail->component( 'button', esc_html__( 'Edit Me', 'awebooking' ) );
