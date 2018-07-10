<?php
/**
 * The template for displaying room title in the template-parts/archive/content.php template
 *
 * This template can be overridden by copying it to {yourtheme}/awebooking/template-parts/archive/title.php.
 *
 * @see      http://docs.awethemes.com/awebooking/developers/theme-developers/
 * @author   awethemes
 * @package  AweBooking
 * @version  3.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

the_title( '<h2 class="list-room__title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h2>' );
