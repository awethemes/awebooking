<?php
/**
 * Single Room type Thumbnails
 *
 * This template can be overridden by copying it to yourtheme/awebooking/single-room-type/room-type-thumbnails.php.
 *
 * @author 		Awethemes
 * @package 	AweBooking/Templates
 * @version     1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $post, $room_type;

$attachment_ids = $room_type->get_gallery_ids();
if ( empty( $attachment_ids ) ) {
	return;
}
?>
<div class="awebooking-room-type-gallery__image">
	<?php
	if ( $attachment_ids && has_post_thumbnail() ) {
		foreach ( $attachment_ids as $attachment_id ) {

			$image = wp_get_attachment_image( $attachment_id, 'awebooking_thumbnail' );

			echo apply_filters( 'awebooking/single_room_type_image_thumbnail_html', $image ); // WPCS: xss ok.
		}
	}
	?>
</div>
