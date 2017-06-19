<?php
/**
 * The Template for displaying room type archives.
 *
 * This template can be overridden by copying it to yourtheme/awebooking/archive-room-type.php.
 *
 * @author 		Awethemes
 * @package 	AweBooking/Templates
 * @version     1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

get_header( 'booking' ); ?>

	<?php
		/**
		 * awebooking/before_main_content hook.
		 *
		 * @hooked abkng_output_content_wrapper - 10 (outputs opening divs for the content)
		 */
		do_action( 'awebooking/before_main_content' );
	?>

		<div class="awebooking-room-type-filter">
		<?php
			/**
			 * awebooking/before_archive_loop hook.
			 *
			 * @hooked abkng_location_filter - 10
			 * @hooked abkng_catalog_ordering - 20
			 */
			do_action( 'awebooking/before_archive_loop' );
		?>
		</div>

		<?php if ( have_posts() ) : ?>

			<?php abkng_room_type_loop_start(); ?>

				<?php while ( have_posts() ) : the_post(); ?>

					<?php abkng_get_template_part( 'content', apply_filters( 'awebooking/content_loop_layout', 'room-type' ) ); ?>

				<?php endwhile; // end of the loop. ?>

			<?php abkng_room_type_loop_end(); ?>

			<?php
				/**
				 * awebooking/after_archive_loop hook.
				 *
				 * @hooked abkng_pagination - 10
				 */
				do_action( 'awebooking/after_archive_loop' );
			?>

		<?php else : ?>
			<?php abkng_get_template( 'loop/no-room-types-found.php' ); ?>

		<?php endif; ?>

	<?php
		/**
		 * awebooking/after_main_content hook.
		 *
		 * @hooked abkng_output_content_wrapper_end - 10 (outputs closing divs for the content)
		 */
		do_action( 'awebooking/after_main_content' );
	?>

	<?php
		/**
		 * awebooking/sidebar hook.
		 *
		 * @hooked abkng_get_sidebar - 10
		 */
		do_action( 'awebooking/sidebar' );
	?>

<?php get_footer( 'booking' );

/* Omit closing PHP tag at the end of PHP files to avoid "headers already sent" issues. */
