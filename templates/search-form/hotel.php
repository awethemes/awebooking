<?php
/**
 * The template for displaying hotel input in the search-form.php template
 *
 * This template can be overridden by copying it to {yourtheme}/awebooking/template-parts/search-form/hotel.php.
 *
 * @see      http://docs.awethemes.com/awebooking/developers/theme-developers/
 * @author   awethemes
 * @package  AweBooking
 * @version  3.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! abrs_multiple_hotels() || abrs_is_room_type() || ! $atts['hotel_location'] ) {
	return;
}

$current_hotel = abrs_http_request()->get( 'hotel' );
if ( ! empty( $atts['only_room'] ) && is_numeric( $atts['only_room'] ) ) {
	$current_hotel = abrs_optional( abrs_get_room_type( $atts['only_room'] ) )->get( 'hotel_id' );
}

?>

<div class="searchbox__box searchbox__box--hotel">
	<div class="searchbox__box-wrap">
		<div class="searchbox__box-line">
			<label class="searchbox__box-label">
				<span><?php esc_html_e( 'Hotel', 'awebooking' ); ?></span>
			</label>

			<div class="searchbox__box-input">
				<select name="hotel" class="searchbox__input searchbox__input--hotel input-transparent">
					<?php foreach ( abrs_list_hotels( [], true ) as $hotel ) : ?>
						<option value="<?php echo esc_attr( $hotel->get_id() ); ?>" <?php selected( $hotel->get_id(), $current_hotel ); ?>><?php echo esc_html( $hotel->get( 'name' ) ); ?></option>
					<?php endforeach; ?>
				</select>
			</div>
		</div>
	</div>
</div>
