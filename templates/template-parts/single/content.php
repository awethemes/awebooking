<?php
/**
 * The template for displaying single room content
 *
 * This template can be overridden by copying it to {yourtheme}/awebooking/template-parts/single/content.php.
 *
 * @see      http://docs.awethemes.com/awebooking/developers/theme-developers/
 * @author   awethemes
 * @package  AweBooking
 * @version  3.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

global $room_type;

do_action( 'abrs_print_notices' );

if ( post_password_required() ) {
	echo get_the_password_form(); // WPCS: XSS ok.
	return;
}
?>

<article id="room-<?php the_ID(); ?>" <?php post_class( 'room' ); ?>>
	<div class="hotel-content">
		<div class="hotel-content__main">
			<header class="room__header">
				<?php the_title( '<h1 class="room__title">', '</h1>' ); ?>

				<p class="room__price">
					<?php
					/* translators: %s room price */
					printf( esc_html__( 'Start from %s/night', 'awebooking' ), '<span>' . abrs_format_price( $room_type->get( 'rack_rate' ) ) . '</span>' ); // WPCS: xss ok.
					?>
				</p>
			</header>

			<div class="room__sections">
				<?php
				/**
				 * abrs_single_room_sections hook.
				 *
				 * @hooked abrs_single_room_description() - 10.
				 * @hooked abrs_single_room_amenities()   - 15.
				 * @hooked abrs_single_room_gallery()     - 20.
				 */
				do_action( 'abrs_single_room_sections' );
				?>
			</div>
		</div><!-- /.hotel-content__main -->

		<aside class="hotel-content__aside">
			<?php
			/**
			 * abrs_single_room_sidebar hook.
			 *
			 * @hooked abrs_single_room_form() - 10.
			 */
			do_action( 'abrs_single_room_sidebar' );
			?>
		</aside><!-- /.hotel-content__aside -->
	</div><!-- /.hotel-content -->
</article><!-- #room-<?php the_ID(); ?> -->
<?php do_action( 'abrs_after_single_room' ); ?>
