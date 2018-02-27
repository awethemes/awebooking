<?php
/**
 * The Template for complete booking.
 *
 * This template can be overridden by copying it to yourtheme/awebooking/complete.php.
 *
 * @author 		Awethemes
 * @package 	Awethemes/Templates
 * @version     3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

get_header();

?>

<p><?php echo sprintf( esc_html__( 'Thanks for your booking. Your booking ID: #%s', 'awebooking' ),1  ); ?></p>

<?php get_footer();
