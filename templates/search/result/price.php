<?php
/**
 * This template show the search result price.
 *
 * This template can be overridden by copying it to {yourtheme}/awebooking/search/result/price.php.
 *
 * @see      http://docs.awethemes.com/awebooking/developers/theme-developers/
 * @author   awethemes
 * @package  AweBooking
 * @version  3.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/* @var \AweBooking\Model\Room_Type $room_type */
/* @var \AweBooking\Availability\Room_Rate $room_rate */

$http_request = abrs_http_request();
$display_price = ( $http_request->get( 'showprice' ) && in_array( $http_request->get( 'showprice' ), [ 'total', 'average', 'first_night' ] ) ) ? $http_request->get( 'showprice' ) : abrs_get_option( 'display_price', 'total' );
?>

<div class="roommaster-inventory roommaster-box">
	<h4 class="roommaster-content__title"><?php esc_html_e( 'Price', 'awebooking' ); ?></h4>

	<?php
	switch ( $display_price ) {
		case 'total':
			abrs_price( $room_rate->get_rate() );
			/* translators: %s nights */
			printf( esc_html_x( 'Cost for %s', 'total cost', 'awebooking' ),
				abrs_ngettext_nights( $room_rate->timespan->nights() )
			); // WPCS: xss ok.
			break;

		case 'average':
			abrs_price( $room_rate->get_price( 'rate_average' ) );
			esc_html_e( 'Average cost per night', 'awebooking' );
			break;

		case 'first_night':
			abrs_price( $room_rate->get_price( 'rate_first_night' ) );
			esc_html_e( 'Cost for first night', 'awebooking' );
			break;
	}
	?>
</div>
