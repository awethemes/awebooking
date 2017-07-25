<?php
namespace AweBooking;

use AweBooking\Support\WP_Object;

class Booking_Item extends WP_Object {
	/**
	 * Name of object type.
	 *
	 * Normally is name of custom-post-type or custom-taxonomy.
	 *
	 * @var string
	 */
	protected $object_type = 'awebooking_item';

	/**
	 * WordPress type for object, Eg: "post" and "term".
	 *
	 * @var string
	 */
	protected $wp_type = 'awebooking_item';

	/**
	 * Type of object metadata is for (e.g., term, post).
	 *
	 * @var string
	 */
	protected $meta_type = 'awebooking_item';

	/**
	 * The attributes for this object.
	 *
	 * Name value pairs (name + default value).
	 *
	 * @var array
	 */
	protected $attributes = [
		'name' => '',
		'booking_id' => 0,
	];

	/**
	 * The attributes that should be cast to native types.
	 *
	 * @var array
	 */
	protected $casts = [
		'booking_id' => 'int',
	];

	/**
	 * An array of attributes mapped with metadata.
	 *
	 * @var array
	 */
	protected $maps = [
		// ...
	];

	/**
	 * Get booking item type.
	 *
	 * !! Overridden by child classes.
	 *
	 * @return string
	 */
	public function get_type() {
		return '';
	}

	/**
	 * Get booking item name.
	 *
	 * @return string
	 */
	public function get_name() {
		return apply_filters( $this->prefix( 'get_name' ), $this['name'], $this );
	}

	/**
	 * Get booking ID this item belongs to.
	 *
	 * @return int
	 */
	public function get_booking_id() {
		return apply_filters( $this->prefix( 'get_booking_id' ), $this['booking_id'], $this );
	}

	/**
	 * Setup the object attributes.
	 *
	 * @return void
	 */
	protected function setup() {
		$this['name'] = $this->instance['name'];
		$this['booking_id'] = absint( $this->instance['booking_id'] );
	}

	/**
	 * Run perform insert object into database.
	 *
	 * @return int|void
	 */
	protected function perform_insert() {
		global $wpdb;

		// Validate before insert, we need a booking ID.
		$booking_id = $this->get_booking_id();
		if ( $booking_id <= 0 ) {
			return;
		}

		$wpdb->insert( $wpdb->prefix . 'awebooking_booking_items',
			[
				'booking_item_name' => $this->get_name(),
				'booking_item_type' => $this->get_type(),
				'booking_id'        => $booking_id,
			],
			[
				'%s',
				'%s',
				'%d',
			]
		);

		return absint( $wpdb->insert_id );
	}

	/**
	 * Run perform update object.
	 *
	 * @param  array $dirty The attributes has been modified.
	 * @return bool|void
	 */
	protected function perform_update( array $dirty ) {
		global $wpdb;

		// Booking-item can be only update the name,
		// so if "name" attribute is not modified, we just return true.
		if ( ! isset( $dirty['name'] ) ) {
			return true;
		}

		$updated = $wpdb->update(
			$wpdb->prefix . 'awebooking_booking_items',
			[ 'booking_item_name' => $this->get_name() ],
			[ 'booking_item_id' => $this->get_id() ]
		);

		return false !== $updated;
	}

	/**
	 * Perform delete object.
	 *
	 * @param  bool $force Force delete or not.
	 * @return bool
	 */
	protected function perform_delete( $force ) {
		global $wpdb;

		$wpdb->query( $wpdb->prepare( "DELETE FROM `{$wpdb->prefix}awebooking_booking_items` WHERE `booking_item_id` = %d", $this->get_id() ) );
		$wpdb->query( $wpdb->prepare( "DELETE FROM `{$wpdb->prefix}awebooking_booking_itemmeta` WHERE `booking_item_id` = %d", $this->get_id() ) );

		return true;
	}

	/**
	 * Setup WP Core Object based on ID and object-type.
	 *
	 * @return void
	 */
	protected function setup_instance() {
		global $wpdb;

		// Try get in the cache first.
		$booking_item = wp_cache_get( $this->get_id(), 'awebooking_cache_booking_item' );

		if ( false === $booking_item ) {
			$booking_item = $wpdb->get_row(
				$wpdb->prepare( "SELECT * FROM `{$wpdb->prefix}awebooking_booking_items` WHERE `booking_item_id` = '%d' LIMIT 1", $this->get_id() ),
				ARRAY_A
			);

			// Do nothing if not found the booking-item.
			if ( is_null( $booking_item ) ) {
				return;
			}

			// Santize before cache this booking-item.
			$booking_item['booking_id'] = (int) $booking_item['booking_id'];
			$booking_item['booking_item_id'] = (int) $booking_item['booking_item_id'];

			wp_cache_add( $this->get_id(), $booking_item, 'awebooking_cache_booking_item' );
		}

		$this->set_instance( $booking_item );
	}
}
