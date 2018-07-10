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
if ( empty( $room_type ) ) {
	return;
}

?>

<article id="room-<?php the_ID(); ?>" <?php post_class( 'list-room' ); ?>>
	<?php
	/**
	 * abrs_before_archive_room hook.
	 *
	 * @hooked abrs_archive_room_thumbnail - 10
	 */
	do_action( 'abrs_before_archive_room' );
	?>

	<div class="list-room__info">
		<header class="list-room__header">
			<?php
			/**
			 * abrs_archive_room_header hook.
			 *
			 * @hooked abrs_archive_room_title - 10
			 * @hooked abrs_archive_room_price - 15
			 */
			do_action( 'abrs_archive_room_header' );
			?>
		</header><!-- /.list-room__header -->

		<div class="list-room__container">
			<?php
			/**
			 * abrs_archive_room_description hook.
			 *
			 * @hooked abrs_archive_room_description - 10
			 */
			do_action( 'abrs_archive_room_description' );
			?>

			<div class="list-room__additional-info">
				<?php
				/**
				 * abrs_archive_room_information hook.
				 *
				 * @hooked abrs_archive_room_information - 10
				 * @hooked abrs_archive_room_occupancy   - 15
				 */
				do_action( 'abrs_archive_room_information' );
				?>
			</div><!-- /.list-room__additional-info -->
		</div><!-- /.list-room__container -->

		<footer class="list-room__footer">
			<?php
			/**
			 * abrs_after_archive_room hook.
			 *
			 * @hooked abrs_archive_room_button - 10
			 */
			do_action( 'abrs_after_archive_room' );
			?>
		</footer><!-- /.list-room__footer -->
	</div><!-- /.list-room__info -->

</article><!-- #room-<?php the_ID(); ?> -->
