<?php
/**
 * The template for displaying room content within loops
 *
 * This template can be overridden by copying it to yourtheme/awebooking/template-parts/archive/content.php.
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

// Ensure visibility.
if ( empty( $room_type ) || ! $room_type->get( 'rack_rate' ) ) {
	return;
}

?>

<article id="room-<?php the_ID(); ?>" <?php post_class( 'list-room' ); ?>>
	<div class="list-room__wrap">
		<div class="list-room__media">
			<?php if ( has_post_thumbnail() ) : ?>
				<a href="<?php echo esc_url( get_the_permalink() ); ?>">
					<?php echo abrs_get_thumbnail(); // WPCS: xss ok. ?>
				</a>
			<?php endif; ?>
		</div>

		<div class="list-room__info">
			<header class="list-room__header">
				<?php the_title( '<h2 class="list-room__title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h2>' ); ?>

				<p class="list-room__price">
					<?php
					/* translators: %s room price */
					printf( esc_html__( 'Start from %s/night', 'awebooking' ), '<span>' . abrs_format_price( $room_type->get( 'rack_rate' ) ) . '</span>' ); // WPCS: xss ok.
					?>
				</p>
			</header><!-- /.list-room__header -->

			<div class="list-room__container">
				<?php if ( $description = $room_type->get( 'short_description' ) ) : ?>

					<div class="list-room__desc">
						<?php print wp_trim_words( $description, 25 ); // WPCS: xss ok. ?>
					</div>

				<?php endif; ?>

				<div class="list-room__additional-info">
					<?php
					abrs_archive_room_information();

					abrs_archive_room_occupancy();
					?>
				</div><!-- /.list-room__additional-info -->
			</div><!-- /.list-room__container -->

			<footer class="list-room__footer">
				<a class="button" href="<?php echo esc_url( get_the_permalink() ); ?>">
					<?php esc_html_e( 'View more infomation', 'awebooking' ); ?>
				</a>
			</footer><!-- /.list-room__footer -->
		</div><!-- /.list-room__info -->
	</div>
</article><!-- #room-<?php the_ID(); ?> -->
