<?php
/**
 * Content wrappers.
 *
 * This template can be overridden by copying it to {yourtheme}/awebooking/template-parts/global/wrapper-start.php.
 *
 * @see      http://docs.awethemes.com/awebooking/developers/theme-developers/
 * @author   awethemes
 * @package  AweBooking
 * @version  3.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$template = get_option( 'template' );

switch ( $template ) {
	case 'twentysixteen':
		echo '<div id="primary" class="content-area twentysixteen"><main id="main" class="site-main" role="main">';
		break;
	default:
		echo '<div id="awebooking-container" class="awebooking-container"><div id="awebooking-main" class="awebooking-main" role="main">';
		break;
}
