<?php
/**
 * Room type attributes
 *
 * Used by list_attributes() in the room type class.
 *
 * This template can be overridden by copying it to yourtheme/awebooking/single-room-type/room-type-attributes.php.
 *
 * @author 		Awethemes
 * @package 	Awethemes/Templates
 * @version     1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$amenities = wp_get_post_terms( get_the_ID(), 'hotel_amenity' );
?>
<?php foreach ( $amenities as $amenity ) : ?>
	<div class="awebooking-amenities__item">
		<h3 class="awebooking-amenities__title"><?php echo $amenity->name; ?></h3>
		<p class="awebooking-amenities__desc"><?php echo $amenity->description; ?></p>
	</div>
<?php endforeach; ?>
