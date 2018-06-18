<?php
/**
 * Content wrappers.
 *
 * This template can be overridden by copying it to {yourtheme}/awebooking/template-parts/global/wrapper-end.php.
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
		echo '</main></div>';
		break;
	default:
		echo '</div></div><!-- /#awebooking-container -->';
		break;
}
