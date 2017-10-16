<?php
namespace AweBooking\Hotel;

use AweBooking\Concierge;
use AweBooking\AweBooking;
use AweBooking\Pricing\Rate;
use AweBooking\Pricing\Price;
use AweBooking\Cart\Buyable;
use AweBooking\Hotel\Service;
use AweBooking\Booking\Request;
use AweBooking\Pricing\Price_Calculator;
use AweBooking\Calculator\Service_Calculator;
use AweBooking\Support\WP_Object;
use AweBooking\Support\Collection;

class Room_Type extends WP_Object implements Buyable {
	/**
	 * This is the name of this object type.
	 *
	 * @var string
	 */
	protected $object_type = AweBooking::ROOM_TYPE;

	/**
	 * The attributes for this object.
	 *
	 * @var array
	 */
	protected $attributes = [
		// Basic infomations.
		'title'             => '',
		'slug'              => '',
		'status'            => '',
		'description'       => '',
		'short_description' => '',
		'date_created'      => null,
		'date_modified'     => null,

		// Room infomations.
		'base_price'        => 0.00,
		'number_adults'     => 0,
		'number_children'   => 0,
		'max_adults'        => 0,
		'max_children'      => 0,
		'minimum_night'     => 0,

		'rooms'             => [],
		'room_ids'          => [],
		'amenities'         => [],
		'amenity_ids'       => [],
		'services'          => [],
		'service_ids'       => [],
		'location'          => null,
		'location_id'       => 0,

		// Extra data.
		'thumbnail_id'      => 0,
		'gallery_ids'       => [],
	];

	/**
	 * The attributes that should be cast to native types.
	 *
	 * @var array
	 */
	protected $casts = [
		'base_price'        => 'float',
		'number_adults'     => 'integer',
		'number_children'   => 'integer',
		'max_adults'        => 'integer',
		'max_children'      => 'integer',
		'minimum_night'     => 'integer',
	];

	/**
	 * An array of meta data mapped with attributes.
	 *
	 * @var array
	 */
	protected $maps = [
		'base_price',
		'number_adults',
		'number_children',
		'max_adults',
		'max_children',
		'minimum_night',
		'gallery_ids'  => 'gallery',
		'thumbnail_id' => '_thumbnail_id',
	];

	/**
	 * //
	 *
	 * @param  array $args //.
	 * @return WP_Query
	 */
	public static function query( array $args = [] ) {
		$query = wp_parse_args( $args, [
			'post_type'        => AweBooking::ROOM_TYPE,
			'booking_adults'   => -1,
			'booking_children' => -1,
			'booking_nights'   => -1,
			'posts_per_page'   => -1,
		]);

		return new \WP_Query( $query );
	}

	/**
	 * Bulk sync rooms.
	 *
	 * TODO: Remove late.
	 *
	 * @param  int   $room_type     The room-type ID.
	 * @param  array $request_rooms The request rooms.
	 * @return void
	 */
	public function bulk_sync_rooms( array $request_rooms ) {
		// Current list room of room-type.
		$db_rooms_ids = array_map( 'absint', $this->list_the_rooms( 'id' ) );

		// Multilanguage need this.
		$room_type_id = apply_filters( $this->prefix( 'get_id_for_rooms' ), $this->get_id() );

		$touch_ids = [];
		foreach ( $request_rooms as $raw_room ) {
			// Ignore in-valid rooms from request.
			if ( ! isset( $raw_room['id'] ) || ! isset( $raw_room['name'] ) ) {
				continue;
			}

			// Sanitize data before working with database.
			$room_args = array_map( 'sanitize_text_field', $raw_room );

			if ( $room_args['id'] > 0 && in_array( (int) $room_args['id'], $db_rooms_ids ) ) {
				$room_unit = new Room( $room_args['id'] );
				$room_unit['name'] = $room_args['name'];
				$room_unit->save();
			} else {
				$room_unit = new Room;
				$room_unit['name'] = $room_args['name'];
				$room_unit['room_type_id'] = $room_type_id;
				$room_unit->save();
			}

			// We'll map current working ID in $touch_ids...
			if ( $room_unit->exists() ) {
				$touch_ids[] = $room_unit->get_id();
			}
		}

		// Fimally, delete invisible rooms.
		$delete_ids = array_diff( $db_rooms_ids, $touch_ids );

		if ( ! empty( $delete_ids ) ) {
			global $wpdb;
			$delete_ids = implode( ',', $delete_ids );

			// @codingStandardsIgnoreLine
			$wpdb->query( "DELETE FROM `{$wpdb->prefix}awebooking_rooms` WHERE `id` IN ({$delete_ids})" );
		}
	}

	/**
	 * Setup the object attributes.
	 *
	 * @return void
	 */
	protected function setup() {
		$this['title']             = $this->instance->post_title;
		$this['slug']              = $this->instance->post_title;
		$this['status']            = $this->instance->post_status;
		$this['description']       = $this->instance->post_content;
		$this['short_description'] = $this->instance->post_excerpt;
		$this['date_created']      = $this->instance->post_date;
		$this['date_modified']     = $this->instance->post_modified;

		if ( $this['gallery_ids'] && ! isset( $this['gallery_ids'][0] ) ) {
			$this['gallery_ids'] = array_keys( $this['gallery_ids'] );
		}

		// Location only have one.
		$hotel_locations = $this->get_term_ids( AweBooking::HOTEL_LOCATION );
		if ( isset( $hotel_locations[0] ) ) {
			$this['location_id'] = $hotel_locations[0];
		}

		$this['amenity_ids'] = $this->get_term_ids( AweBooking::HOTEL_AMENITY );
		$this['service_ids'] = $this->get_term_ids( AweBooking::HOTEL_SERVICE );
		$this['room_ids']    = $this->list_the_rooms( 'id' );

		/**
		 * Fire 'awebooking/room_type/after_setup' action.
		 *
		 * @param Room_Type $room_type The room type object instance.
		 */
		do_action( $this->prefix( 'after_setup' ), $this );
	}

	/**
	 * Run perform insert object into database.
	 *
	 * @see wp_insert_post()
	 *
	 * @return bool
	 */
	protected function perform_insert() {
		$insert_id = wp_insert_post([
			'post_type'    => $this->object_type,
			'post_title'   => $this->get_title(),
			'post_content' => $this->get_description(),
			'post_excerpt' => $this->get_short_description(),
			'post_status'  => $this->get_status(),
		], true );

		if ( ! is_wp_error( $insert_id ) ) {
			return $insert_id;
		}
	}

	/**
	 * Run perform update object.
	 *
	 * @see wp_update_post()
	 *
	 * @param  array $dirty The attributes changed.
	 * @return bool
	 */
	protected function perform_update( array $dirty ) {
		$postarr = [];

		// Only update the post when the post data dirty.
		if ( isset( $dirty['title'] ) ) {
			$postarr['post_title'] = $this->get_title();
		}

		if ( isset( $dirty['description'] ) ) {
			$postarr['post_content'] = $this->get_description();
		}

		if ( isset( $dirty['short_description'] ) ) {
			$postarr['post_excerpt'] = $this->get_short_description();
		}

		if ( ! empty( $postarr ) ) {
			$updated = wp_update_post(
				array_merge( $postarr, [ 'ID' => $this->get_id() ] ), true
			);

			return ! is_wp_error( $updated );
		}

		return true;
	}

	/**
	 * The title.
	 *
	 * @return string
	 */
	public function get_title() {
		return apply_filters( $this->prefix( 'get_title' ), $this['title'], $this );
	}

	/**
	 * The status.
	 *
	 * @return string
	 */
	public function get_status() {
		return apply_filters( $this->prefix( 'get_status' ), $this['status'], $this );
	}

	/**
	 * The description.
	 *
	 * @return string
	 */
	public function get_description() {
		return apply_filters( $this->prefix( 'get_description' ), $this['description'], $this );
	}

	/**
	 * The short description.
	 *
	 * @return string
	 */
	public function get_short_description() {
		return apply_filters( $this->prefix( 'get_short_description' ), $this['short_description'], $this );
	}

	/**
	 * Get base price.
	 *
	 * @return Price
	 */
	public function get_base_price() {
		return apply_filters( $this->prefix( 'get_base_price' ), new Price( $this['base_price'] ), $this );
	}

	/**
	 * Get number adults available for this room-type.
	 *
	 * @return int
	 */
	public function get_number_adults() {
		return apply_filters( $this->prefix( 'get_number_adults' ), $this['number_adults'], $this );
	}

	/**
	 * Get max overflow adults.
	 *
	 * @return int
	 */
	public function get_max_adults() {
		return apply_filters( $this->prefix( 'get_max_adults' ), $this['max_adults'], $this );
	}

	/**
	 * Returns allowed number adults for this room-type.
	 *
	 * @return int
	 */
	public function get_allowed_adults() {
		return $this->get_number_adults() + $this->get_max_adults();
	}

	/**
	 * Get number children available for this room-type.
	 *
	 * @return int
	 */
	public function get_number_children() {
		return apply_filters( $this->prefix( 'get_number_children' ), $this['number_children'], $this );
	}

	/**
	 * Get max overflow children.
	 *
	 * @return int
	 */
	public function get_max_children() {
		return apply_filters( $this->prefix( 'get_max_children' ), $this['max_children'], $this );
	}

	/**
	 * Returns allowed number children for this room-type.
	 *
	 * @return int
	 */
	public function get_allowed_children() {
		return $this->get_number_children() + $this->get_max_children();
	}

	/**
	 * Get minimum nights.
	 *
	 * @return int
	 */
	public function get_minimum_night() {
		return apply_filters( $this->prefix( 'get_minimum_night' ), $this['minimum_night'], $this );
	}

	/**
	 * Get list IDs of rooms.
	 *
	 * @return array
	 */
	public function get_room_ids() {
		return apply_filters( $this->prefix( 'get_room_ids' ), $this['room_ids'], $this );
	}

	/**
	 * Return total rooms.
	 *
	 * @return int
	 */
	public function get_total_rooms() {
		return count( $this->get_room_ids() );
	}

	/**
	 * Get rooms of this room type.
	 *
	 * @return array An array of Room instance.
	 */
	public function get_rooms() {
		// If we have any rooms, and see empty the rooms, let build it.
		if ( empty( $this['rooms'] ) ) {
			$this['rooms'] = awebooking_map_instance(
				$this->get_room_ids(), Room::class
			);
		}

		return apply_filters( $this->prefix( 'get_rooms' ), $this['rooms'], $this );
	}

	/**
	 * Get collection of rates.
	 *
	 * @return Collection
	 */
	public function get_rates() {
		return Collection::make( get_children([
			'post_parent' => $this->get_id(),
			'post_type'   => AweBooking::PRICING_RATE,
			'orderby'     => 'menu_order',
			'order'       => 'ASC',
		]))->map(function( $post ) {
			return new Rate( $post->ID, $this );
		})->prepend(
			$this->get_standard_rate()
		);
	}

	/**
	 * Get standard rate.
	 *
	 * @return Rate
	 */
	public function get_standard_rate() {
		return new Rate( $this->get_id(), $this );
	}

	/**
	 * Get room location.
	 *
	 * @return WP_Term|null
	 */
	public function get_location() {
		$location = get_term( $this['location_id'], AweBooking::HOTEL_LOCATION );

		if ( is_null( $location ) || is_wp_error( $location ) ) {
			$location = null;
		}

		return apply_filters( $this->prefix( 'get_location' ), $location, $this );
	}

	public function get_amenities() {
	}

	/**
	 * Get room services or extra-services.
	 *
	 * @return array
	 */
	public function get_services() {
		$services = [];

		foreach ( $this['service_ids'] as $service ) {
			$services[] = new Service( $service );
		}

		return apply_filters( $this->prefix( 'get_services' ), $services, $this );
	}

	/**
	 * Get room thumbnail ID.
	 *
	 * @return int
	 */
	public function get_thumbnail_id() {
		return apply_filters( $this->prefix( 'get_thumbnail_id' ), $this['thumbnail_id'], $this );
	}

	/**
	 * Get gallery IDs.
	 *
	 * @return int
	 */
	public function get_gallery_ids() {
		return apply_filters( $this->prefix( 'get_gallery_ids' ), $this['gallery_ids'], $this );
	}

	/**
	 * Clean object cache after saved.
	 *
	 * @return void
	 */
	protected function clean_cache() {
		wp_cache_delete( $this->get_id(), 'awebooking/rooms_in_room_types' );
	}

	/**
	 * Retrieve the rooms of this room type.
	 *
	 * @param string $pluck //.
	 *
	 * @return false|array
	 */
	public function list_the_rooms( $pluck = null ) {
		global $wpdb;

		// Multilanguage need this.
		$room_type_id = apply_filters( $this->prefix( 'get_id_for_rooms' ), $this->get_id() );

		// First, try get the rooms in cache.
		$the_rooms = wp_cache_get( $room_type_id, 'awebooking/rooms_in_room_types' );

		// If not found we will fetch from database.
		if ( false === $the_rooms ) {
			// Get rooms in a room type from database, limit 100 results for performance.
			$the_rooms = $wpdb->get_results(
				$wpdb->prepare( "SELECT * FROM `{$wpdb->prefix}awebooking_rooms` WHERE `room_type` = '%d' ORDER BY LENGTH(`name`) ASC, `name` ASC LIMIT 1000", $room_type_id ),
				ARRAY_A
			);

			if ( is_null( $the_rooms ) ) {
				return false;
			}

			// Loop throuth the results, sanitize each room, and add to cache.
			foreach ( $the_rooms as &$r ) {
				$r['id'] = absint( $r['id'] );
				$r['room_type'] = absint( $r['room_type'] );

				wp_cache_add( $r['id'], $r, 'awebooking/room' );
				unset( $r );
			}

			// Add this results to cache.
			wp_cache_add( $room_type_id, $the_rooms, 'awebooking/rooms_in_room_types' );
		}

		return $pluck ? wp_list_pluck( $the_rooms, $pluck ) : $the_rooms;
	}

	/**
	 * Get the identifier of the Buyable item.
	 *
	 * @return int|string
	 */
	public function get_buyable_identifier( $options ) {
		return $this->get_id();
	}

	/**
	 * Get the price of the Buyable item.
	 *
	 * @return float
	 */
	public function get_buyable_price( $options ) {
		$options['room-type'] = $this->get_id();
		$request = Request::from_array( $options->to_array() );

		// Price by nights.
		$price = Concierge::get_room_price( $this, $request );
		$pipes = apply_filters( $this->prefix( 'get_buyable_price' ), [], $this, $request );

		if ( $request->has_request( 'extra_services' ) ) {
			foreach ( $request->get_services() as $service_id => $quantity ) {
				$pipes[] = new Service_Calculator( new Service( $service_id ), $request, $price );
			}
		}

		return (new Price_Calculator( $price ))
			->through( $pipes )
			->process();
	}

	/**
	 * Determines the Buyable item is purchasable.
	 *
	 * @return boolean
	 */
	public function is_purchasable( $options ) {
		if ( $this->get_base_price()->is_zero() ) {
			return false;
		}

		try {
			$request = Request::from_array( $options->to_array() );
			$availability = Concierge::check_room_type_availability( $this, $request );

			return $availability->available();
		} catch ( \Exception $e ) {
			//
		}
	}
}
