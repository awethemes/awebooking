<?php
/**
 * AweBooking bootstrap file.
 *
 * @package AweBooking
 */

/**
 * We need autoload via Composer to make everything works.
 */
require trailingslashit( __DIR__ ) . 'vendor/autoload.php';

require_once trailingslashit( __DIR__ ) . 'inc/Support/helpers.php';

// Try locate the Skeleton.
if ( file_exists( __DIR__ . '/vendor/awethemes/skeleton/skeleton.php' ) ) {
	require_once trailingslashit( __DIR__ ) . '/vendor/awethemes/skeleton/skeleton.php';
} elseif ( ! defined( 'AWETHEMES_SKELETON_LOADED' ) ) {
	wp_die( 'AweBooking can\'t works without the Skeleton. Please double-check that everything is setup correctly!' );
}

require_once trailingslashit( __DIR__ ) . 'inc/functions.php';

// Make AweBooking\AweBooking as AweBooking alias.
class_alias( 'AweBooking\\AweBooking', 'AweBooking' );

// Back-compatibility.
class_alias( 'AweBooking\\Model\\WP_Object', 'AweBooking\\Support\\WP_Object' );
class_alias( 'AweBooking\\Model\\Amenity', 'AweBooking\\Hotel\\Amenity' );
class_alias( 'AweBooking\\Model\\Service', 'AweBooking\\Hotel\\Service' );
class_alias( 'AweBooking\\Model\\Room_Type', 'AweBooking\\Hotel\\Room_Type' );
class_alias( 'AweBooking\\Model\\Room', 'AweBooking\\Hotel\\Room' );

class_alias( 'AweBooking\Formatting', 'AweBooking\Support\Formatting' );
class_alias( 'AweBooking\Calendar\Period\Period', 'AweBooking\Support\Period' );
class_alias( 'AweBooking\Calendar\Period\Period_Collection', 'AweBooking\Support\Period_Collection' );

class_alias( 'AweBooking\\Money\\Currency', 'AweBooking\\Currency\\Currency' );
class_alias( 'AweBooking\\Money\\Currencies\\Currencies', 'AweBooking\\Currency\\Currency_Manager' );

class_alias( 'AweBooking\\Template', 'AweBooking\\Support\\Template' );

class_alias( 'AweBooking\\Deprecated\\Concierge', 'AweBooking\\Concierge' );
class_alias( 'AweBooking\\Deprecated\\Support\\Abstract_Calendar', 'AweBooking\\Support\\Abstract_Calendar' );

class_alias( 'AweBooking\Deprecated\Booking\Request', 'AweBooking\Booking\Request' );
class_alias( 'AweBooking\Deprecated\Booking\Calendar', 'AweBooking\Booking\Calendar' );
class_alias( 'AweBooking\Deprecated\Booking\Availability', 'AweBooking\Booking\Availability' );

class_alias( 'AweBooking\Deprecated\Booking\Events\Only_Days_Trait', 'AweBooking\Booking\Events\Only_Days_Trait' );
class_alias( 'AweBooking\Deprecated\Booking\Events\Rate_Pricing', 'AweBooking\Booking\Events\Rate_Pricing' );
class_alias( 'AweBooking\Deprecated\Booking\Events\Room_Booking', 'AweBooking\Booking\Events\Room_Booking' );
class_alias( 'AweBooking\Deprecated\Booking\Events\Room_State', 'AweBooking\Booking\Events\Room_State' );
class_alias( 'AweBooking\Deprecated\Booking\BAT\Unit_Trait', 'AweBooking\Booking\BAT\Unit_Trait' );
class_alias( 'AweBooking\Calendar\Provider\Stores\BAT_Store', 'AweBooking\Booking\Store' );
