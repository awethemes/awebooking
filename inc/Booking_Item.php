<?php
namespace AweBooking;

use AweBooking\Support\WP_Object;

class Booking_Item extends WP_Object {
	/**
	 * The booking object instance this item belong to.
	 *
	 * @var Booking
	 */
	protected $booking;

	/**
	 * The booking item object instance.
	 *
	 * @var Booking_Item|mixed
	 */
	protected $parent;

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
	protected $meta_type = 'booking_item';

	/**
	 * The attributes for this object.
	 *
	 * Name value pairs (name + default value).
	 *
	 * @var array
	 */
	protected $attributes = [
		'name'       => '',
		'parent_id'  => 0,
		'booking_id' => 0,
	];

	/**
	 * The attributes that should be cast to native types.
	 *
	 * @var array
	 */
	protected $casts = [
		'parent_id'  => 'int',
		'booking_id' => 'int',
	];

	/**
	 * Booking item constructor.
	 *
	 * @param mixed $object Object ID we'll working for.
	 */
	public function __construct( $object = 0 ) {
		if ( property_exists( $this, 'extra_attributes' ) ) {
			$this->attributes = array_merge( $this->attributes, $this->extra_attributes );
		}

		if ( property_exists( $this, 'extra_casts' ) ) {
			$this->casts = array_merge( $this->casts, $this->extra_casts );
		}

		parent::__construct( $object );
	}

	/**
	 * Setup the object attributes.
	 *
	 * @return void
	 */
	protected function setup() {
		$this['name']       = $this->instance['booking_item_name'];
		$this['parent_id']  = absint( $this->instance['booking_item_parent'] );
		$this['booking_id'] = absint( $this->instance['booking_id'] );

		$this->booking = Factory::get_booking( $this['booking_id'] );

		if ( $this['parent_id'] ) {
			$this->parent = Factory::get_booking_item( $this['parent_id'] );
		}
	}

	/**
	 * Returns booking item type.
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
	 * Returns the booking instance.
	 *
	 * @return Booking|null
	 */
	public function get_booking() {
		return $this->booking;
	}

	/**
	 * Get booking ID this item belongs to.
	 *
	 * @return int
	 */
	public function get_booking_id() {
		return $this->get_attribute( 'booking_id' );
	}

	/**
	 * Set the booking ID this item belongs to.
	 *
	 * @param  int $booking_id The booking ID.
	 * @return $this
	 */
	public function set_booking_id( $booking_id ) {
		$this->attributes['booking_id'] = absint( $booking_id );

		return $this;
	}

	/**
	 * Returns the parent object.
	 *
	 * @return Bookign_Item|mixed|null
	 */
	public function get_parent() {
		return $this->parent;
	}

	/**
	 * Get parent ID this item belongs to.
	 *
	 * @return int
	 */
	public function get_parent_id() {
		return $this->get_attribute( 'parent_id' );
	}

	/**
	 * Set the parent ID this item belongs to.
	 *
	 * @param  int $parent_id The parent ID.
	 * @return $this
	 */
	public function set_parent_id( $parent_id ) {
		$this->attributes['parent_id'] = absint( $parent_id );

		return $this;
	}

	/**
	 * Return edit URL.
	 *
	 * @param  array $query_args Additional query args.
	 * @return string|null
	 */
	public function get_edit_url( array $query_args = [] ) {
		if ( ! $this->exists() ) {
			return;
		}

		$query_args = array_merge([
			'booking' => $this->get_booking_id(),
			'item'    => $this->get_id(),
		], $query_args );

		return add_query_arg( $query_args,
			admin_url( 'admin.php?page=awebooking-edit-item' )
		);
	}

	/**
	 * Return delete URL.
	 *
	 * @return string|null
	 */
	public function get_delete_url() {
		if ( ! $this->exists() ) {
			return;
		}

		$post_type   = get_post_type_object( AweBooking::BOOKING );
		$delete_link = admin_url( sprintf( $post_type->_edit_link, $this->get_booking_id() ) );

		$delete_link = add_query_arg([
			'action'    => 'delete_awebooking_item',
			'item'      => $this->get_id(),
			'item_type' => $this->get_type(),
		], $delete_link );

		return wp_nonce_url( $delete_link, "delete_item_awebooking_{$this->get_booking_id()}" );
	}

	/**
	 * Clean object cache after saved.
	 *
	 * @return void
	 */
	protected function clean_cache() {
		wp_cache_delete( $this->get_id(), 'awebooking_cache_booking_item' );
	}

	/**
	 * Run perform insert object into database.
	 *
	 * @return int|void
	 */
	protected function perform_insert() {
		global $wpdb;

		// We need a booking ID.
		$booking_id = $this->get_booking_id();
		if ( $booking_id <= 0 ) {
			return;
		}

		$wpdb->insert( $wpdb->prefix . 'awebooking_booking_items',
			[
				'booking_item_name'   => $this->get_name(),
				'booking_item_type'   => $this->get_type(),
				'booking_item_parent' => $this->get_parent_id(),
				'booking_id'          => $booking_id,
			],
			[
				'%s',
				'%s',
				'%d',
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

		if ( ! $this->is_dirty( 'name', 'parent_id', 'booking_id' ) ) {
			return true;
		}

		// We need a booking ID.
		$booking_id = $this->get_booking_id();
		if ( $booking_id <= 0 ) {
			return;
		}

		$updated = $wpdb->update( $wpdb->prefix . 'awebooking_booking_items',
			[
				'booking_id'          => $booking_id,
				'booking_item_name'   => $this->get_name(),
				'booking_item_parent' => $this->get_parent_id(),
			],
			[
				'booking_item_id' => $this->get_id(),
			]
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
