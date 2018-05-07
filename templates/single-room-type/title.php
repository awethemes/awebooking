<?php
/**
 * Single Room type title
 *
 * This template can be overridden by copying it to yourtheme/awebooking/single-room-type/title.php.
 *
 * @see      http://docs.awethemes.com/awebooking/developers/theme-developers/
 * @author   awethemes
 * @package  AweBooking
 * @version  3.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

the_title( '<h1 class="awebooking-room-type__title h2">', '</h1>' );
