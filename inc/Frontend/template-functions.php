<?php
/**
 * Functions for the templates.
 *
 * @package AweBooking
 */

/**
 * Prints messages and errors which are stored in the flash.
 *
 * @return void
 */
function awebooking_print_notices() {
	abrs_get_template( 'notices.php', [ 'messages' => abrs_flash()->all() ] );
}

/**
 * Display search result item.
 *
 * @param  \AweBooking\Availability\Request   $res_request    The current reservation request.
 * @param  \AweBooking\Model\Room_Type        $room_type      The room type instance.
 * @param  \AweBooking\Availability\Room_Rate $room_rate      The room rate instance.
 * @param  array                              $availabilities An array of availabilities.
 *
 * @return void
 * @access private
 */
function awebooking_search_result_item( $res_request, $room_type, $room_rate, $availabilities ) {
	abrs_get_template( 'search/result-item.php', get_defined_vars() );
}

/**
 * Show the checkout guest controls.
 *
 * @access private
 */
function awebooking_checkout_guest_details() {
	abrs_get_template( 'checkout/form-guest-details.php', [ 'controls' => abrs_checkout()->get_controls() ] );
}

/**
 * Show the checkout additionals controls.
 *
 * @access private
 */
function awebooking_checkout_additionals() {
	abrs_get_template( 'checkout/form-additionals.php', [ 'controls' => abrs_checkout()->get_controls() ] );
}

/**
 * Show the payment methods on the checkout.
 *
 * @access private
 */
function awebooking_checkout_payments() {
	abrs_get_template( 'checkout/payments.php', [
		'checkout'    => abrs_checkout(),
		'gateways'    => awebooking( 'gateways' )->enabled(),
		'button_text' => apply_filters( 'abrs_booking_button_text', esc_html__( 'Book Now', 'awebooking' ) ),
	]);
}
