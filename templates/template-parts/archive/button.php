<?php
/**
 * The template for displaying room button in the template-parts/archive/content.php template
 *
 * This template can be overridden by copying it to {yourtheme}/awebooking/template-parts/archive/button.php.
 *
 * @see      http://docs.awethemes.com/awebooking/developers/theme-developers/
 * @author   awethemes
 * @package  AweBooking
 * @version  3.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<a class="button" href="<?php echo esc_url( get_the_permalink() ); ?>">
	<?php esc_html_e( 'View more infomation', 'awebooking' ); ?>
</a>
