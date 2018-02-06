<?php
namespace AweBooking;

class Constants {
	const DATE_FORMAT       = 'Y-m-d';
	const JS_DATE_FORMAT    = 'yy-mm-dd';

	// The room-type defined.
	const BOOKING           = 'awebooking';
	const ROOM_TYPE         = 'room_type';
	const PRICING_RATE      = 'pricing_rate';
	const HOTEL_LOCATION    = 'hotel_location';
	const HOTEL_AMENITY     = 'hotel_amenity';
	const HOTEL_SERVICE     = 'hotel_extra_service';

	/* Position constants */
	const CURRENCY_POS_LEFT        = 'left';
	const CURRENCY_POS_RIGHT       = 'right';
	const CURRENCY_POS_LEFT_SPACE  = 'left_space';
	const CURRENCY_POS_RIGHT_SPACE = 'right_space';

	// The availability state.
	const STATE_AVAILABLE   = 0;
	const STATE_UNAVAILABLE = 1;
	const STATE_PENDING     = 2;
	const STATE_BOOKED      = 3;

	const CAPABILITY_MANAGER      = 'manage_awebooking';

	// The menu page constants.
	const ADMIN_PAGE_HOTEL   = 'edit.php?post_type=room_type';
	const ADMIN_PAGE_BOOKING = 'admin.php?page=awebooking';

	// The cache group.
	const CACHE_ROOM_UNIT          = 'awebooking_cache_room_object';
	const CACHE_ROOM_TYPE          = 'awebooking_cache_room_type_object';
	const CACHE_BOOKING            = 'awebooking_cache_booking_object';
	const CACHE_BOOKING_ITEM       = 'awebooking_cache_booking_item_object';

	const CACHE_RAW_ROOM_UNIT      = 'awebooking_cache_raw_room';
	const CACHE_RAW_TAX_RATE       = 'awebooking_cache_raw_tax';
	const CACHE_ROOMS_IN_ROOM_TYPE = 'awebooking_cache_rooms_in_room_type';

	// The tax amount type.
	const TAX_AMOUNT_PERCENTAGE = 'percentage';
	const TAX_AMOUNT_FIXED      = 'fixed';
}
