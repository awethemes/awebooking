<?php
namespace AweBooking\Booking\Items;

use AweBooking\Factory;
use AweBooking\AweBooking;
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
		$booking = Factory::get_booking( $this['booking_id'] );

		return apply_filters( $this->prefix( 'get_booking' ), $booking, $this );
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
	 * @return mixed|null
	 */
	public function get_item_parent() {
		if ( ! $this->get_parent_id() ) {
			return;
		}

		$parent = Factory::get_booking_item( $this->get_parent_id() );

		return apply_filters( $this->prefix( 'get_item_parent' ), $parent, $this );
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
	 * Returns item quantity.
	 *
	 * @return int
	 */
	public function get_quantity() {
		return 1;
	}

	/**
	 * Type checking.
	 *
	 * @param  string|array $type Booking type(s) to check.
	 * @return bool
	 */
	public function is_type( $type ) {
		return in_array( $this->get_type(), (array) $type );
	}

	/**
	 * Determines if the current item is able to be saved.
	 *
	 * @return bool
	 */
	public function can_save() {
		$the_booking = $this->get_booking();

		// Require a exists booking.
		if ( ! $the_booking->exists() ) {
			return false;
		}

		return true;
	}

	/**
	 * Returns delete URL.
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

		if ( ! $this->can_save() ) {
			return -1;
		}

		$wpdb->insert( $wpdb->prefix . 'awebooking_booking_items',
			[
				'booking_item_name'   => $this->get_name(),
				'booking_item_type'   => $this->get_type(),
				'booking_item_parent' => $this->get_parent_id(),
				'booking_id'          => $this->get_booking_id(),
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

		// Nothing to perform update, so we'll return "true"
		// to tell WP_Object run next action.
		if ( ! $this->is_dirty( 'name', 'parent_id', 'booking_id' ) ) {
			return true;
		}

		if ( ! $this->can_save() ) {
			return false;
		}

		$updated = $wpdb->update( $wpdb->prefix . 'awebooking_booking_items',
			[
				'booking_id'          => $this->get_booking_id(),
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
	 * @param  bool $force Not used.
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
			$type_query = '';
			if ( $this->get_type() ) {
				$type_query = "AND `booking_item_type` = '" . esc_sql( $this->get_type() ) . "' ";
			}

			$booking_item = $wpdb->get_row(
				// @codingStandardsIgnoreLine
				$wpdb->prepare( "SELECT * FROM `{$wpdb->prefix}awebooking_booking_items` WHERE `booking_item_id` = '%d' {$type_query} LIMIT 1", $this->get_id() ),
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
