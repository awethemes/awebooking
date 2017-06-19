<?php
/**
 * Optional Amenities tab
 *
 * This template can be overridden by copying it to yourtheme/awebooking/single-room-type/tabs/amenity.php.
 *
 * @author        Awethemes
 * @package       AweBooking/Templates
 * @version       1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

global $room_type;

$heading = esc_html( apply_filters( 'awebooking/room_type_amenities_heading', __( 'Amenities', 'awebooking' ) ) );

?>

<?php if ( $heading ) : ?>
	<h2 hidden><?php echo esc_html( $heading ); ?></h2>
<?php endif; ?>

<?php
/**
 * awebooking/room_type_amenities hook.
 *
 * @hooked abkng_display_room_type_attributes - 10
 */
do_action( 'awebooking/room_type_amenities', $room_type ); ?>
