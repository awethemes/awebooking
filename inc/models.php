<?php

use AweBooking\Model\Room;
use AweBooking\Model\Room_Type;
use AweBooking\Model\Booking;
use AweBooking\Model\Booking\Item;

/**
 * Returns room object by given ID.
 *
 * @param  mixed $room The room ID.
 * @return \AweBooking\Model\Room|null
 */
function abrs_get_room( $room ) {
	$room = new Room( $room );

	return $room->exists() ? $room : null;
}

/**
 * Main function for returning the room type.
 *
 * @param  mixed $room_type The post object or post ID of the room type.
 * @return \AweBooking\Model\Room_Type|false|null
 */
function abrs_get_room_type( $room_type ) {
	return abrs_rescue( function() use ( $room_type ) {
		$room_type = new Room_Type( $room_type );

		return $room_type->exists() ? $room_type : null;
	}, false );
}

/**
 * Main function for returning the booking.
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
 * Get a booking item.
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
