<?php
/**
 * The template for displaying room description in the template-parts/archive/content.php template
 *
 * This template can be overridden by copying it to {yourtheme}/awebooking/template-parts/archive/description.php.
 *
 * @see      http://docs.awethemes.com/awebooking/developers/theme-developers/
 * @author   awethemes
 * @package  AweBooking
 * @version  3.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $room_type;

if ( ! $description = $room_type->get( 'short_description' ) ) {
	return;
}

?>

<div class="list-room__desc">
	<?php print wp_trim_words( $description, 25, '...' ); // WPCS: xss ok. ?>
</div>
