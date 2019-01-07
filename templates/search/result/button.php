<?php
/**
 * This template show the search result button.
 *
 * This template can be overridden by copying it to {yourtheme}/awebooking/search/result/button.php.
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

$rooms_left = $room_rate->get_remain_rooms()->count();

?>

<div class="roommaster-button roommaster-box">
	<?php
	abrs_book_room_button([
		'room_type'   => $room_type->get_id(),
		'show_button' => true,
		'button_atts' => [
			'class' => 'booknow button is-primary',
		],
	]);
	?>

	<span class="roommaster-button__remaining-rooms">
		<?php
		if ( $rooms_left <= 2 ) {
			/* translators: %s Number of remain rooms */
			printf( esc_html( _nx( 'Only %s room left', 'Only %s rooms left', $rooms_left, 'remain rooms', 'awebooking' ) ), esc_html( number_format_i18n( $rooms_left ) ) );
		} else {
			/* translators: %s Number of remain rooms */
			printf( esc_html_x( '%s rooms left', 'remain rooms', 'awebooking' ), esc_html( number_format_i18n( $rooms_left ) ) );
		}
		?>
	</span>
</div>
