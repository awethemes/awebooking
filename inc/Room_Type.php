<?php
namespace AweBooking;

use AweBooking\Pricing\Price;
use AweBooking\Support\WP_Object;

class Room_Type extends WP_Object {
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
		'number_adults'     => 2,
		'number_children'   => 0,
		'max_adults'        => 0,
		'max_children'      => 0,
		'minimum_night'     => 1,

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
		'gallery' => 'gallery_ids',
	];

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

		$this['thumbnail_id'] = (int) get_post_thumbnail_id( $this->get_id() );
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
	 * @return integer
	 */
	public function get_number_adults() {
		return apply_filters( $this->prefix( 'get_number_adults' ), $this['number_adults'], $this );
	}

	/**
	 * Get number children available for this room-type.
	 *
	 * @return integer
	 */
	public function get_number_children() {
		return apply_filters( $this->prefix( 'get_number_children' ), $this['number_children'], $this );
	}

	/**
	 * Get max overflow children.
	 *
	 * @return integer
	 */
	public function get_max_children() {
		return apply_filters( $this->prefix( 'get_max_children' ), $this['max_children'], $this );
	}

	/**
	 * Get max overflow adults.
	 *
	 * @return integer
	 */
	public function get_max_adults() {
		return apply_filters( $this->prefix( 'get_max_adults' ), $this['max_adults'], $this );
	}

	/**
	 * Get minimum nights.
	 *
	 * @return integer
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
	 * @return integer
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
			$the_rooms = $this->get_room_ids();

			// Loop through the rooms and create the Room instance.
			foreach ( $the_rooms as &$room ) {
				$room = new Room( $room );
			}

			// Apply the rooms object.
			$this['rooms'] = $the_rooms;
		}

		return apply_filters( $this->prefix( 'get_rooms' ), $this['rooms'], $this );
	}

	/**
	 * //
	 *
	 * @return array
	 */
	public function get_rates() {
		return get_children( [
			'post_parent' => $this->get_id(),
			'post_type'   => 'awebooking_rate',
			'numberposts' => -1,
			'post_status' => 'publish',
		], ARRAY_A );
	}

	/**
	 * Get standard rate.
	 *
	 * @return Rate
	 */
	public function get_standard_rate() {
		return new Rate( $this->get_id(), $this->get_base_price()->to_amount() );
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
	 * @return integer
	 */
	public function get_thumbnail_id() {
		return apply_filters( $this->prefix( 'get_thumbnail_id' ), $this['thumbnail_id'], $this );
	}

	/**
	 * Get gallery IDs.
	 *
	 * @return integer
	 */
	public function get_gallery_ids() {
		return apply_filters( $this->prefix( 'get_gallery_ids' ), $this['gallery_ids'], $this );
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

		// First, try get the rooms in cache.
		$the_rooms = wp_cache_get( $this->get_id(), 'awebooking/rooms_in_room_types' );

		// If not found we will fetch from database.
		if ( false === $the_rooms ) {
			// Get rooms in a room type from database, limit 100 results for performance.
			$the_rooms = $wpdb->get_results(
				$wpdb->prepare( "SELECT * FROM `{$wpdb->prefix}awebooking_rooms` WHERE `room_type` = '%d' ORDER BY LENGTH(`name`) ASC, `name` ASC LIMIT 100", $this->get_id() ),
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
			}

			// Add this results to cache.
			wp_cache_add( $this->get_id(), $the_rooms, 'awebooking/rooms_in_room_types' );
		}

		return $pluck ? wp_list_pluck( $the_rooms, $pluck ) : $the_rooms;
	}
}
