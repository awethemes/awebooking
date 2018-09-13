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

$hotel = abrs_get_hotel( get_the_ID() );
?>

<article id="hotel-<?php the_ID(); ?>" <?php post_class( 'list-hotel' ); ?>>
	<div class="list-hotel__wrap">
		<div class="list-hotel__media">
			<?php if ( has_post_thumbnail() ) : ?>
				<a href="<?php echo esc_url( get_the_permalink() ); ?>">
					<?php echo abrs_get_thumbnail(); // WPCS: xss ok. ?>
				</a>
			<?php endif; ?>
		</div>

		<div class="list-hotel__info">
			<header class="list-hotel__header">
				<?php the_title( '<h2 class="list-hotel__title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h2>' ); ?>
			</header><!-- /.list-hotel__header -->

			<div class="list-hotel__container">
				<?php if ( get_the_excerpt() ) : ?>
					<div class="list-hotel__desc">
						<?php print wp_trim_words( get_the_excerpt(), 25 ); // WPCS: xss ok. ?>
					</div>
				<?php endif; ?>

				<div class="list-hotel__additional-info">
					<?php printf( esc_html__( 'Address: %s', 'awebooking' ), $hotel->get( 'hotel_address' ) ); ?>
				</div><!-- /.list-hotel__additional-info -->
			</div><!-- /.list-hotel__container -->

			<footer class="list-hotel__footer">
				<a class="button" href="<?php echo esc_url( get_the_permalink() ); ?>">
					<?php esc_html_e( 'View more infomation', 'awebooking' ); ?>
				</a>
			</footer><!-- /.list-hotel__footer -->
		</div><!-- /.list-hotel__info -->
	</div>
</article><!-- #hotel-<?php the_ID(); ?> -->
