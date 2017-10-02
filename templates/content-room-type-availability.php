<?php
/**
 * The template for displaying room type content within loops
 *
 * This template can be overridden by copying it to yourtheme/awebooking/content-room-type-availability.php.
 *
 * @author  Awethemes
 * @package AweBooking/Templates
 * @version 3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// Ensure visibility.
if ( empty( $result ) ) {
	return;
}

$room_type_id = $result->get_room_type()->get_id();

?>
<li <?php post_class( 'awebooking-loop-room-type' ); ?>>

	<div class="awebooking-loop-room-type__media">
		<a href="<?php echo esc_url( get_the_permalink( $room_type_id ) ) ?>">
		<?php
		if ( has_post_thumbnail( $room_type_id ) ) {
			echo get_the_post_thumbnail( $room_type_id, 'awebooking_catalog' );
		} elseif ( awebooking_placeholder_img_src() ) {
			echo awebooking_placeholder_img( 'awebooking_catalog' ); // WPCS: xss ok.
		}
		?>
		</a>

	</div>

	<div class="awebooking-loop-room-type__info">
		<h2 class="awebooking-loop-room-type__title">
			<a href="<?php echo esc_url( get_permalink( $room_type_id ) ); ?>" rel="bookmark">
				<?php echo esc_html( $result->get_room_type()->get_title() );?>
			</a>
		</h2>


		<p class="awebooking-loop-room-type__price">
			<span><?php

			switch ( awebooking_option( 'showing_price' ) ) {
				case 'average_price':
					printf( esc_html__( 'Average: %s/night', 'awebooking' ), $result->get_price_average() );
					break;

				case 'total_price':
					printf( esc_html__( 'Total: %s', 'awebooking' ), $result->get_total_price() );
					break;

				default:
					printf( esc_html__( 'From: %s/night', 'awebooking' ), $result->get_room_type()->get_base_price() );
					break;
			}
			?></span>
		</p>

		<div class="awebooking-loop-room-type__desc">
			<?php print wp_trim_words( $result->get_room_type()->get_description(), 25, '...' ); // WPCS: xss ok. ?>
		</div>

		<?php
			$default_args = awebooking_get_booking_request_query( array( 'room-type' => $room_type_id ) );
			$detail_url         = add_query_arg( $default_args, get_the_permalink( $room_type_id ) );
		?>
		<a class="awebooking-loop-room-type__button" href="<?php echo esc_url( $detail_url ); ?>"><?php esc_html_e( 'View more infomation', 'awebooking' ); ?></a><br />

		<?php
			$booking_url = add_query_arg( array_merge( array( 'booking-action' => 'view' ), (array) $default_args ), awebooking_get_page_permalink( 'booking' ) );
		?>
		<a class="awebooking-loop-room-type__button-booking" href="<?php echo esc_url( $booking_url ); ?>"><?php esc_html_e( 'Book Room', 'awebooking' ); ?></a>

	</div>
</li>
