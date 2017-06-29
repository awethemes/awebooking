<?php
/**
 * The Template for displaying check availability page.
 *
 * This template can be overridden by copying it to yourtheme/awebooking/check-availability.php.
 *
 * @author 		Awethemes
 * @package 	AweBooking/Templates
 * @version     1.0.0
 */

use AweBooking\Room_Type;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
?>
<div class="">

	<?php if ( $errors ) : ?>
		<div class="awebooking-notice">
			<?php print $errors; // WPCS: xss ok. ?>
		</div>
	<?php endif; ?>

	<?php if ( isset( $results ) && $results ) : ?>

		<?php abkng_room_type_loop_start(); ?>

			<?php foreach ( $results as $post => $result ) : ?>

				<?php abkng_get_template( 'content-room-type-availability.php', array( 'result' => $result ) ); ?>

			<?php endforeach; // end of the loop.
			wp_reset_postdata(); ?>

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
</div>
