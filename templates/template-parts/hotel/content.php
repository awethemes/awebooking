<?php
/**
 * The template for displaying single hotel content
 *
 * This template can be overridden by copying it to {yourtheme}/awebooking/template-parts/hotel/content.php.
 *
 * @see      http://docs.awethemes.com/awebooking/developers/theme-developers/
 * @author   awethemes
 * @package  AweBooking
 * @version  3.1.8
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

do_action( 'abrs_print_notices' );

if ( post_password_required() ) {
	echo get_the_password_form(); // WPCS: XSS ok.
	return;
}
?>

<article id="hotel-<?php the_ID(); ?>" <?php post_class( 'hotel' ); ?>>
	<header class="hotel__header">
		<?php the_title( '<h1 class="hotel__title">', '</h1>' ); ?>
	</header>

	<div class="hotel__sections">
		<?php
		/**
		 * abrs_single_hotel_sections hook.
		 *
		 * @hooked abrs_single_hotel_description() - 10.
		 * @hooked abrs_single_hotel_rooms()       - 20.
		 */
		do_action( 'abrs_single_hotel_sections' );
		?>
	</div>
</article><!-- #hotel-<?php the_ID(); ?> -->
<?php do_action( 'abrs_after_single_hotel' ); ?>
