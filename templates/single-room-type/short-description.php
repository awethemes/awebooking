<?php
/**
 * Single Room Type short description
 *
 * This template can be overridden by copying it to yourtheme/awebooking/single-room-type/short-description.php.
 *
 *
 * @author 		Awethemes
 * @package 	AweBooking/Templates
 * @version     1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

global $post;

if ( ! $post->post_excerpt ) {
	return;
}

?>
<div class="awebooking-loop-room-type__short_description">
    <?php echo apply_filters( 'awebooking/short_description', $post->post_excerpt ); ?>
</div>
