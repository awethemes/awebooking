<?php
/**
 * The template for displaying room type content within loops
 *
 * This template can be overridden by copying it to yourtheme/awebooking/content-room.php.
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
if ( empty( $room_type ) ) {
	return;
}
?>

<article id="room-type-<?php the_ID(); ?>" <?php post_class( 'list-room' ); ?>>

	<div class="list-room__media">

		<a href="<?php echo esc_url( get_the_permalink() ); ?>">
			<?php abrs_template_room_thumbnail(); ?>
		</a>
	</div>

	<div class="list-room__info">
		<header class="list-room__header">
			<?php the_title( '<h2 class="list-room__title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h2>' ); ?>

			<?php if ( $room_type->get( 'rack_rate' ) ) : ?>
				<p class="list-room__price">
					<?php
					/* translators: %s room price */
					printf( esc_html__( 'Start from %s/night', 'awebooking' ), '<span>' . abrs_format_price( $room_type->get( 'rack_rate' ) ) . '</span>' ); // WPCS: xss ok.
					?>
				</p>
			<?php endif; ?>
		</header><!-- /.list-room__header -->

		<div class="list-room__container">
			<div class="list-room__desc">
				<?php print wp_trim_words( $room_type->get( 'short_description' ), 25, '...' ); // WPCS: xss ok. ?>
			</div>

			<ul class="list-room__info-list">
				<?php if ( $room_type->get( 'view' ) ) : ?>
					<li class="info-item">
						<span class="info-icon">
							<i class="aficon aficon-business"></i>
							<span class="screen-reader-text"><?php echo esc_html_x( 'Room view', 'room view button', 'awebooking' ); ?></span>
						</span>
						<?php echo esc_html( $room_type->get( 'view' ) ); ?>
					</li>
				<?php endif; ?>

				<?php if ( $room_type->get( 'area_size' ) ) : ?>
					<li class="info-item">
						<span class="info-icon">
							<i class="aficon aficon-elevator"></i>
							<span class="screen-reader-text"><?php echo esc_html_x( 'Area size', 'area size button', 'awebooking' ); ?></span>
						</span>
						<?php
							/* translators: %1$s area size, %2$s measure unit */
							printf( esc_html_x( '%1$s %2$s', 'room area size', 'awebooking' ),
								esc_html( $room_type->get( 'area_size' ) ),
								abrs_get_measure_unit_label()
							); // WPCS: xss ok.
						?>
					</li>
				<?php endif; ?>

				<?php if ( $room_type->get( 'beds' ) ) : ?>
					<li class="info-item">
						<span class="info-icon">
							<i class="aficon aficon-bed"></i>
							<span class="screen-reader-text"><?php echo esc_html_x( 'Bed', 'bed button', 'awebooking' ); ?></span>
						</span>
						<?php print abrs_get_room_beds( $room_type ); // WPCS: xss ok. ?>
					</li>
				<?php endif; ?>

				<?php do_action( 'awebooking/result_item/after_room_informations' ); ?>
			</ul>

			<div class="list-room__occupancy">
				<?php if ( $room_type->get( 'number_adults' ) ) : ?>
					<span class="roommaster-occupancy__item">
						<?php
							/* translators: %1$s number adults, %2$s adult button */
							printf( esc_html_x( '%1$s x %2$s', 'number adults', 'awebooking' ),
								absint( $room_type->get( 'number_adults' ) ),
								'<i class="aficon aficon-man"></i><span class="screen-reader-text">' . esc_html_x( 'Adult', 'adult button', 'awebooking' ) . '</span>'
							);
						?>
					</span>
				<?php endif; ?>

				<?php if ( $room_type->get( 'number_children' ) ) : ?>
					<span class="roommaster-occupancy__item">
						<?php
							/* translators: %1$s number children, %2$s child button */
							printf( esc_html_x( '%1$s x %2$s', 'number children', 'awebooking' ),
								absint( $room_type->get( 'number_children' ) ),
								'<i class="aficon aficon-body"></i><span class="screen-reader-text">' . esc_html_x( 'Child', 'child button', 'awebooking' ) . '</span>'
							);
						?>

					</span>
				<?php endif; ?>

				<?php if ( $room_type->get( 'number_infants' ) ) : ?>
					<span class="roommaster-occupancy__item">
						<?php
							/* translators: %1$s number infants, %2$s infant button */
							printf( esc_html_x( '%1$s x %2$s', 'number infants', 'awebooking' ),
								absint( $room_type->get( 'number_infants' ) ),
								'<i class="aficon aficon-infant"></i><span class="screen-reader-text">' . esc_html_x( 'Infant', 'infant button', 'awebooking' ) . '</span>'
							);
						?>
					</span>
				<?php endif; ?>
			</div>
		</div><!-- /.list-room__container -->

		<footer class="list-room__footer">
			<a class="button" href="<?php echo esc_url( get_the_permalink() ); ?>">
				<?php esc_html_e( 'View more infomation', 'awebooking' ); ?>
			</a>
		</footer><!-- /.list-room__footer -->
	</div><!-- /.list-room__info -->

</article><!-- #room-type-<?php the_ID(); ?> -->
