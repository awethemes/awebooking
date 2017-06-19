<?php
/**
 * Description tab
 *
 * This template can be overridden by copying it to yourtheme/awebooking/single-room-type/tabs/description.php.
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

global $post;

$heading = esc_html( apply_filters( 'awebooking/room_type_description_heading', __( 'Description', 'awebooking' ) ) );

?>

<?php if ( $heading ) : ?>
  <h2 hidden><?php echo $heading; ?></h2>
<?php endif; ?>

<?php the_content(); ?>
