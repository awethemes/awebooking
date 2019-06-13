<?php
/**
 * This template show the search results.
 *
 * This template can be overridden by copying it to {yourtheme}/awebooking/search/results.php.
 *
 * @see      http://docs.awethemes.com/awebooking/developers/theme-developers/
 * @author   awethemes
 * @package  AweBooking
 * @version  3.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/* @var \AweBooking\Availability\Query_Results $results */

$res_request = $results->get_request();

?>

<div class="hotel-content">
	<div class="hotel-content__main">
		<?php do_action( 'abrs_before_search_results', $results, $res_request ); ?>

		<div id="search-rooms-results" class="rooms rooms--search">
			<?php
			foreach ( $results as $availabilities ) {
				list( $room_type, $room_rate ) = array_values( $availabilities );

				/**
				 * Fire action to display search result item.
				 *
				 * @hooked awebooking_search_result_item()
				 *
				 * @param \AweBooking\Availability\Request   $res_request    The current reservation request.
				 * @param \AweBooking\Model\Room_Type        $room_type      The room type instance.
				 * @param \AweBooking\Availability\Room_Rate $room_rate      The room rate instance.
				 */
				do_action( 'abrs_display_search_result_item', $res_request, $room_type, $room_rate, $availabilities );
			}

			if ( has_action( 'abrs_display_search_result_unavailable_item' ) ) {
				foreach ( $results->get_invalid_items() as $availabilities ) {
					list( $room_type, $room_rate ) = array_values( $availabilities );

					/**
					 * Fire action to display search result unavailable item.
					 *
					 * @hooked awebooking_search_result_item()
					 *
					 * @param \AweBooking\Availability\Request   $res_request    The current reservation request.
					 * @param \AweBooking\Model\Room_Type        $room_type      The room type instance.
					 * @param \AweBooking\Availability\Room_Rate $room_rate      The room rate instance.
					 */
					do_action( 'abrs_display_search_result_unavailable_item', $res_request, $room_type, $room_rate );
				}
			} else if ( abrs_blank( $results ) ) {
				abrs_get_template( 'search/no-results.php' );
			}
			?>
		</div><!-- /.rooms -->

		<?php do_action( 'abrs_after_search_results', $results, $res_request ); ?>
	</div>

	<aside class="hotel-content__aside">
		<?php abrs_get_template( 'reservation/reservation.php' ); ?>

		<?php if ( ! abrs_reservation()->is_empty() ) : ?>

			<div class="reservation-goto-checkout">
				<a href="<?php echo esc_url( add_query_arg( 'res', $res_request->get_hash(), abrs_get_checkout_url() ) ); ?>" class="button button--primary button--block"><?php esc_html_e( 'Checkout', 'awebooking' ); ?></a>
			</div>

		<?php endif; ?>

	</aside>
</div><!-- /.hotel-content -->
