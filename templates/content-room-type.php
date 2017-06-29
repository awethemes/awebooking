<?php
/**
 * The template for displaying room type content within loops
 *
 * This template can be overridden by copying it to yourtheme/awebooking/content-room-type.php.
 *
 * @author  Awethemes
 * @package AweBooking/Templates
 * @version 1.0.0
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
<li <?php post_class( 'awebooking-loop-room-type' ); ?>>

	<div class="awebooking-loop-room-type__media">

		<?php
		/**
		 * awebooking/before_archive_loop_item hook.
		 *
		 * @hooked abkng_template_loop_room_type_link_open - 10
		 */
		do_action( 'awebooking/before_archive_loop_item' );

		/**
		 * awebooking/before_archive_loop_item_title hook.
		 *
		 * @hooked abkng_template_loop_room_type_thumbnail - 10
		 * @hooked abkng_template_loop_room_type_link_close - 20
		 */
		do_action( 'awebooking/before_archive_loop_item_title' ); ?>

	</div>

	<div class="awebooking-loop-room-type__info">

		<?php
		/**
		 * awebooking/archive_loop_item_title hook.
		 *
		 * @hooked abkng_template_loop_room_type_title - 10
		 */
		do_action( 'awebooking/archive_loop_item_title' );

		/**
		 * awebooking/after_archive_loop_item_title hook.
		 *
		 * @hooked abkng_template_loop_price - 10
		 * @hooked abkng_template_loop_description - 20
		 */
		do_action( 'awebooking/after_archive_loop_item_title' );

		/**
		 * awebooking/after_archive_loop_item hook.
		 *
		 * @hooked abkng_template_loop_view_more - 10
		 */
		do_action( 'awebooking/after_archive_loop_item' ); ?>

	</div>
</li>

