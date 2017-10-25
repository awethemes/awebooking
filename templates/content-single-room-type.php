<?php
/**
 * The template for displaying room_type content in the single-room_type.php template
 *
 * This template can be overridden by copying it to yourtheme/awebooking/content-single-room_type.php.
 *
 * @author 		Awethemes
 * @package 	AweBooking/Templates
 * @version     3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( post_password_required() ) {
	echo get_the_password_form(); // WPCS: xss ok.
	return;
}
?>
<div id="room-type-<?php the_ID(); ?>" <?php post_class( 'awebooking-room-type' ); ?>>
	<div class="awebooking-room-type__wrapper">

		<div class="awebooking-room-type__header">
			<?php
				awebooking_template_single_title();
				awebooking_template_single_price();
			?>
		</div>

		<div class="awebooking-room-type__media">

			<?php
				/**
				 * awebooking/before_single_room_type_summary hook.
				 *
				 * @hooked awebooking_show_room_type_images - 20
				 */
				do_action( 'awebooking/before_single_room_type_summary' );
			?>
		</div>

		<div class="awebooking-room-type__check-form summary entry-summary">

			<?php
				/**
				 * awebooking/single_room_type_summary hook.
				 *
				 * @hooked awebooking_template_single_title - 5
				 * @hooked awebooking_template_single_price - 10
				 * @hooked awebooking_template_single_form - 15
				 */
				do_action( 'awebooking/single_room_type_summary' );
			?>

		</div><!-- .summary -->

	</div>

	<div class="awebooking-room-type__tabs">
		<?php
			/**
			 * awebooking/after_single_room_type_summary hook.
			 *
			 * @hooked awebooking_output_room_type_data_tabs - 10
			 */
			do_action( 'awebooking/after_single_room_type_summary' );
		?>
	</div>

</div><!-- #room-type-<?php the_ID(); ?> -->

<?php do_action( 'awebooking/after_single_room_type' ); ?>
