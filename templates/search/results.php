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

?>

<?php do_action( 'awebooking_print_notices' ); ?>

<div class="search-rooms columns">
	<div class="search-rooms__main column is-9">
		<?php do_action( 'awebooking_before_search_results', $results, $res_request ); ?>

		<div id="search-rooms-results" class="rooms rooms--search">
			<?php
			foreach ( $results as $availabilities ) {
				if ( ! isset( $availabilities['room_type'], $availabilities['room_rate'] ) ) {
					continue;
				}

				// Extract the availabilities.
				list( $room_type, $room_rate ) = [ $availabilities['room_type'], $availabilities['room_rate'] ];

				/**
				 * Fire action to display search result item.
				 *
				 * @hooked awebooking_search_result_item()
				 *
				 * @param \AweBooking\Availability\Request   $res_request    The current reservation request.
				 * @param \AweBooking\Model\Room_Type        $room_type      The room type instance.
				 * @param \AweBooking\Availability\Room_Rate $room_rate      The room rate instance.
				 * @param array                              $availabilities An array of availabilities.
				 */
				do_action( 'awebooking_display_search_result_item', $res_request, $room_type, $room_rate, $availabilities );
			}
			?>
		</div><!-- /.rooms -->

		<?php do_action( 'awebooking_after_search_results', $results, $res_request ); ?>
	</div>

	<aside class="search-rooms__aside column is-3">
		<?php abrs_get_template( 'reservation/booked.php' ); ?>
	</aside>
</div><!-- /.container -->
