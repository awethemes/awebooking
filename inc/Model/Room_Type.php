<?php
namespace AweBooking\Model;

use AweBooking\Constants;
use AweBooking\AweBooking;
use AweBooking\Pricing\Rate;
use AweBooking\Pricing\Price;
use AweBooking\Cart\Buyable;
use AweBooking\Model\Service;
use AweBooking\Support\Collection;
use AweBooking\Deprecated\Model\Room_Type_Deprecated;

class Room_Type extends WP_Object implements Buyable {
	use Traits\Room_Type\Basic_Attributes_Trait,
		Traits\Room_Type\Occupancy_Attributes_Trait,
		Traits\Room_Type\Room_Units_Trait,
		Room_Type_Deprecated;

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
		'minimum_night'     => 0,

		'maximum_occupancy'   => 0,
		'number_adults'       => 0,
		'number_children'     => 0,
		'number_infants'      => 0,
		'calculation_infants' => false,

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
		'base_price'          => 'float',
		'minimum_night'       => 'integer',

		'maximum_occupancy'   => 'integer',
		'number_adults'       => 'integer',
		'number_children'     => 'integer',
		'number_infants'      => 'integer',
		'calculation_infants' => 'boolean',
	];

	/**
	 * An array of meta data mapped with attributes.
	 *
	 * @var array
	 */
	protected $maps = [
		'base_price'        => 'base_price',
		'minimum_night'     => 'minimum_night',

		'maximum_occupancy' => '_maximum_occupancy',
		'number_adults'     => 'number_adults',
		'number_children'   => 'number_children',
		'number_infants'    => 'number_infants',

		'gallery_ids'       => 'gallery',
		'thumbnail_id'      => '_thumbnail_id',
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
			'booking_infants'  => -1,
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
		$db_rooms_ids = array_map( 'absint', $this->get_rooms()->pluck( 'id' )->all() );

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
				$room_unit['room_type'] = $room_type_id;
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
	 * Get base price.
	 *
	 * @return Price
	 */
	public function get_base_price() {
		return apply_filters( $this->prefix( 'get_base_price' ), new Price( $this['base_price'] ), $this );
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
	 * Setup the object attributes.
	 *
	 * @return void
	 */
	protected function setup() {
		$this['title']             = $this->instance->post_title;
		$this['slug']              = $this->instance->post_name;
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

		/**
		 * Fire 'awebooking/room_type/after_setup' action.
		 *
		 * @param Room_Type $room_type The room type object instance.
		 */
		do_action( $this->prefix( 'after_setup' ), $this );
	}

	/**
	 * Clean object cache after saved.
	 *
	 * @return void
	 */
	protected function clean_cache() {
		wp_cache_delete( $this['room_type'], Constants::CACHE_ROOMS_IN_ROOM_TYPE );
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
}
