<?php

use Illuminate\Support\Arr;
use AweBooking\Model\Booking;
use AweBooking\Model\Booking\Item;

/**
 * Retrieves the booking object.
 *
 * @param  mixed $booking The post object or post ID of the booking.
 * @return \AweBooking\Model\Booking|false|null
 */
function abrs_get_booking( $booking ) {
	return abrs_rescue( function() use ( $booking ) {
		$booking = new Booking( $booking );

		return $booking->exists() ? $booking : null;
	}, false );
}

/**
 * Retrieves the booking item.
 *
 * @param  mixed $item The item ID or item array.
 * @return \AweBooking\Model\Booking\Item|false|null
 */
function abrs_get_booking_item( $item ) {
	// Given a numeric, let's get item from DB.
	if ( is_numeric( $item ) ) {
		$item = abrs_db_booking_item( $item );
	}

	// Try to resolve the item type.
	if ( $item instanceof Item ) {
		$item_id   = $item->get_id();
		$item_type = $item->get_type();
	} elseif ( is_array( $item ) && ! empty( $item['booking_item_type'] ) ) {
		$item_id   = $item['booking_item_id'];
		$item_type = $item['booking_item_type'];
	}

	// If can't resolve the item type, just leave.
	if ( ! isset( $item_id, $item_type ) ) {
		return false;
	}

	$classmap = abrs_booking_item_classmap();
	if ( ! array_key_exists( $item_type, $classmap ) ) {
		return false;
	}

	// Apply filters allow users can overwrite the class name.
	$classname = apply_filters( 'awebooking/get_booking_item_classname', $classmap[ $item_type ], $item_type, $item_id );

	return abrs_rescue( function() use ( $classname, $item_id ) {
		$item = new $classname( $item_id );

		return $item->exists() ? $item : null;
	}, false );
}

/**
 * Returns an array of booking item classmap.
 *
 * @return array
 */
function abrs_booking_item_classmap() {
	return apply_filters( 'awebooking/booking_items_classmap', [
		'line_item'    => \AweBooking\Model\Booking\Room_Item::class,
		'payment_item' => \AweBooking\Model\Booking\Payment_Item::class,
	]);
}

/**
 * Returns a list of booking statuses.
 *
 * @return array
 */
function abrs_list_booking_statuses() {
	return apply_filters( 'awebooking/list_booking_statuses', [
		'awebooking-pending'     => _x( 'Pending', 'Booking status', 'awebooking' ),
		'awebooking-on-hold'     => _x( 'Reserved', 'Booking status', 'awebooking' ),
		'awebooking-deposit'     => _x( 'Deposit', 'Booking status', 'awebooking' ),
		'awebooking-inprocess'   => _x( 'Processing', 'Booking status', 'awebooking' ),
		'awebooking-completed'   => _x( 'Paid', 'Booking status', 'awebooking' ),
		'checked-in'             => _x( 'Checked In', 'Booking status', 'awebooking' ),
		'checked-out'            => _x( 'Checked Out', 'Booking status', 'awebooking' ),
		'awebooking-cancelled'   => _x( 'Cancelled', 'Booking status', 'awebooking' ),
	]);
}

/**
 * Get the nice name for an booking status.
 *
 * @param  string $status The status name.
 * @return string
 */
function abrs_get_booking_status_name( $status ) {
	$statuses = abrs_list_booking_statuses();

	$status = ( 0 === strpos( $status, 'awebooking-' ) ) ? substr( $status, 11 ) : $status;

	if ( array_key_exists( $status, $statuses ) ) {
		return $statuses[ $status ];
	}

	return Arr::get( $statuses, 'awebooking-' . $status, $status );
}
