<?php
/**
 * Content wrappers
 *
 * This template can be overridden by copying it to yourtheme/awebooking/global/wrapper-end.php.
 *
 * @author 		Awethemes
 * @package 	AweBooking/Templates
 * @version     1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$template = get_option( 'template' );

switch ( $template ) {
	case 'twentysixteen' :
		echo '</main></div>';
		break;
	default :
		echo '</div></div>';
		break;
}
