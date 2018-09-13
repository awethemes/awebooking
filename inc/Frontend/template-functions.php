<?php
/**
 * Functions for the templates.
 *
 * @package AweBooking
 */

/**
 * Prints messages and errors which are stored in the flash.
 *
 * @access private
 */
function abrs_print_notices() {
	abrs_get_template( 'notices.php', [ 'messages' => abrs_flash()->all() ] );
}

/**
 * Display the search form on the search page.
 *
 * @access private
 */
function abrs_search_form_on_search() {
	abrs_get_template( 'search/search-form.php' );
}

/**
 * Display the filter form on the search page.
 *
 * @access private
 */
function abrs_filter_form() {
	abrs_get_template( 'search/filter-form.php' );
}

/**
 * Display search result item.
 *
 * @param  \AweBooking\Availability\Request   $res_request    The current reservation request.
 * @param  \AweBooking\Model\Room_Type        $room_type      The room type instance.
 * @param  \AweBooking\Availability\Room_Rate $room_rate      The room rate instance.
 * @param  array                              $availabilities An array of availabilities.
 *
 * @access private
 */
function abrs_search_result_item( $res_request, $room_type, $room_rate, $availabilities ) {
	abrs_get_template( 'search/result-item.php', get_defined_vars() );
}

/**
 * Gets search result room list.
 *
 * @access private
 */
function abrs_search_result_header( $room_type, $room_rate ) {
	abrs_get_template( 'search/result/header.php', compact( 'room_type', 'room_rate' ) );
}

/**
 * Gets search result room type.
 *
 * @access private
 */
function abrs_search_result_room_type( $room_type, $room_rate ) {
	abrs_get_template( 'search/result/room-type.php', compact( 'room_type', 'room_rate' ) );
}

/**
 * Gets search result room list.
 *
 * @access private
 */
function abrs_search_result_room_price( $room_type, $room_rate ) {
	abrs_get_template( 'search/result/price.php', compact( 'room_type', 'room_rate' ) );
}

/**
 * Show the checkout services.
 *
 * @access private
 */
function abrs_checkout_services() {
	abrs_get_template( 'checkout/services.php', [
		'services' => abrs_list_services(),
		'includes' => abrs_reservation()->get_included_services(),
	]);
}

/**
 * Show the checkout guest controls.
 *
 * @access private
 */
function abrs_checkout_guest_details() {
	abrs_get_template( 'checkout/form-guest-details.php', [ 'controls' => abrs_checkout()->get_controls() ] );
}

/**
 * Show the checkout additionals controls.
 *
 * @access private
 */
function abrs_checkout_additionals() {
	abrs_get_template( 'checkout/form-additionals.php', [ 'controls' => abrs_checkout()->get_controls() ] );
}

/**
 * Show the payment methods on the checkout.
 *
 * @access private
 */
function abrs_checkout_payments() {
	abrs_get_template( 'checkout/payments.php', [
		'checkout'    => abrs_checkout(),
		'gateways'    => abrs_payment_gateways()->get_enabled(),
		'button_text' => apply_filters( 'abrs_booking_button_text', esc_html__( 'Book Now', 'awebooking' ) ),
	]);
}

/**
 * Get the terms and conditons checkbox text.
 *
 * @since 3.1.8
 *
 * @return string
 */
function abrs_get_terms_and_conditions_checkbox_text() {
	$terms_page_id = abrs_get_page_id( 'terms' );
	$terms_link    = $terms_page_id ? '<a href="' . esc_url( get_permalink( $terms_page_id ) ) . '" target="_blank">' . esc_html__( 'terms and conditions', 'awebooking' ) . '</a>' : esc_html__( 'terms and conditions', 'awebooking' );

	/* translators: %s terms and conditions page name and link */
	$text = sprintf( __( 'I have read and agree to the website %s', 'awebooking' ), '[terms]' );

	return trim( apply_filters( 'abrs_get_terms_and_conditions_checkbox_text', str_replace( '[terms]', $terms_link, $text ) ) );
}

/* Globals */

/**
 * Output the start of the page wrapper.
 *
 * @access private
 */
function abrs_content_wrapper_before() {
	abrs_get_template( 'template-parts/global/wrapper-start.php' );
}

/**
 * Output the end of the page wrapper.
 *
 * @access private
 */
function abrs_content_wrapper_after() {
	abrs_get_template( 'template-parts/global/wrapper-end.php' );
}

/* Single templates */

/**
 * Gets single room description.
 *
 * @access private
 */
function abrs_single_room_description() {
	abrs_get_template_part( 'template-parts/single/description' );
}

/**
 * Gets single room amenities.
 *
 * @access private
 */
function abrs_single_room_amenities() {
	abrs_get_template_part( 'template-parts/single/amenities' );
}

/**
 * Gets single room gallery.
 *
 * @access private
 */
function abrs_single_room_gallery() {
	abrs_get_template_part( 'template-parts/single/gallery' );
}

/**
 * Gets single room form.
 *
 * @access private
 */
function abrs_single_room_form() {
	abrs_get_template_part( 'template-parts/single/form' );
}

/* Archive templates */

/**
 * Gets archive pagination.
 *
 * @access private
 */
function abrs_archive_pagination() {
	abrs_get_template_part( 'template-parts/archive/pagination' );
}

/**
 * Gets archive room information.
 */
function abrs_archive_room_information() {
	abrs_get_template_part( 'template-parts/archive/information' );
}

/**
 * Gets archive room occupancy.
 */
function abrs_archive_room_occupancy() {
	abrs_get_template_part( 'template-parts/archive/occupancy' );
}

/* Hotel templates */

/**
 * Gets single hotel description.
 *
 * @access private
 */
function abrs_single_hotel_description() {
	abrs_get_template_part( 'template-parts/hotel/description' );
}

/**
 * Gets single hotel description.
 *
 * @access private
 */
function abrs_single_hotel_rooms() {
	abrs_get_template_part( 'template-parts/hotel/rooms' );
}
