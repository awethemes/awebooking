<?php
/**
 * The Template for displaying check availability page.
 *
 * This template can be overridden by copying it to yourtheme/awebooking/check-availability.php.
 *
 * @author 		Awethemes
 * @package 	AweBooking/Templates
 * @version     3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

do_action( 'awebooking/template_notices' );
?>
<div class="">

	<?php if ( $errors ) : ?>
		<div class="awebooking-notice">
			<?php print $errors; // WPCS: xss ok. ?>
		</div>
	<?php else : ?>

		<?php if ( isset( $results ) && count( $results ) > 0 ) : ?>

			<?php awebooking_room_type_loop_start(); ?>

				<?php foreach ( $results as $post => $result ) : ?>

					<?php awebooking_get_template( 'content-room-type-availability.php', array( 'result' => $result ) ); ?>

				<?php endforeach; // end of the loop.
				wp_reset_postdata(); ?>

			<?php awebooking_room_type_loop_end(); ?>

			<?php
				/**
				 * awebooking/after_archive_loop hook.
				 *
				 * @hooked awebooking_pagination - 10
				 */
				do_action( 'awebooking/after_archive_loop' );
			?>
		<?php else : ?>

			<?php awebooking_get_template( 'loop/no-room-types-found.php' ); ?>

		<?php endif; ?>
	<?php endif; ?>
</div>
