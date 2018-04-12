<?php
namespace AweBooking;

class Constants {
	// The core constants.
	const BOOKING           = 'awebooking';
	const ROOM_TYPE         = 'room_type';
	const HOTEL_RATE        = 'hotel_rate';
	const HOTEL_RATE_PLAN   = 'hotel_rate_plan';
	const HOTEL_LOCATION    = 'hotel_location';
	const HOTEL_AMENITY     = 'hotel_amenity';
	const HOTEL_SERVICE     = 'hotel_extra_service';

	// Booking constants.
	const STATE_BOOKED      = 3;
	const STATE_PENDING     = 2;
	const STATE_UNAVAILABLE = 1;
	const STATE_AVAILABLE   = 0;
}
