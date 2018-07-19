<?php
/**
 * This template show the search result header.
 *
 * This template can be overridden by copying it to {yourtheme}/awebooking/search/result/header.php.
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

?>

<h3 class="roommaster-header__title">
	<a href="<?php echo esc_url( get_permalink( $room_type->get_id() ) ); ?>" rel="bookmark" target="_blank">
		<?php echo esc_html( $room_type->get( 'title' ) ); ?>
	</a>
</h3>

<!-- <button class="button button--secondary button--circle-icon">
	<span class="aficon aficon-arrow-down"></span>
</button> -->
