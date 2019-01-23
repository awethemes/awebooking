<?php

namespace AweBooking\Model\Booking;

use AweBooking\Model\Model;

abstract class Item extends Model {
	/**
	 * Name of object type.
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
	 * Name of item type.
	 *
	 * @var string
	 */
	protected $type = 'line_item';

	/**
	 * The attributes for this object.
	 *
	 * @var array
	 */
	protected $attributes = [
		'name'       => '',
		'parent_id'  => 0,
		'booking_id' => 0,
	];

	/**
	 * Constructor.
	 *
	 * @param mixed $object Object ID we'll working for.
	 */
	public function __construct( $object = 0 ) {
		$this->setup_attributes();

		$this->map_attributes();

		parent::__construct( $object );
	}

	/**
	 * Gets name of item type.
	 *
	 * @return string
	 */
	public function get_type() {
		return $this->type;
	}

	/**
	 * Gets the booking object.
	 *
	 * @return \AweBooking\Model\Booking|null
	 */
	public function get_booking() {
		return $this->attributes['booking_id'] ? abrs_get_booking( $this->get( 'booking_id' ) ) : null;
	}

	/**
	 * Gets the parent item object.
	 *
	 * @return \AweBooking\Model\Booking\Item|null
	 */
	public function get_parent() {
		return $this->attributes['parent_id'] ? abrs_get_booking_item( $this->get( 'parent_id' ) ) : null;
	}

	/**
	 * {@inheritdoc}
	 */
	protected function setup() {
		$this['name']       = $this->instance['booking_item_name'];
		$this['parent_id']  = absint( $this->instance['booking_item_parent'] );
		$this['booking_id'] = absint( $this->instance['booking_id'] );
	}

	/**
	 * Clean object cache after saved.
	 *
	 * @return void
	 */
	protected function clean_cache() {
		wp_cache_delete( $this->get( 'booking_id' ), 'awebooking_booking_items' );

		abrs_flush_booking_item_cache( $this->id );
	}

	/**
	 * {@inheritdoc}
	 */
	protected function setup_instance() {
		$booking_item = abrs_get_raw_booking_item( $this->get_id(), $this->get_type() );

		if ( ! is_null( $booking_item ) ) {
			$this->set_instance( $booking_item );
		}
	}

	/**
	 * {@inheritdoc}
	 */
	protected function finish_save() {
		parent::finish_save();

		if ( method_exists( $this, 'saved' ) ) {
			$this->saved();
		}

		if ( $booking = abrs_get_booking( $this->get( 'booking_id' ) ) ) {
			$booking->calculate_totals();
		}
	}

	/**
	 * {@inheritdoc}
	 */
	protected function perform_insert() {
		global $wpdb;

		$wpdb->insert( $wpdb->prefix . 'awebooking_booking_items',
			[
				'booking_item_name'   => $this->get( 'name' ),
				'booking_item_type'   => $this->get_type(),
				'booking_item_parent' => $this->get( 'parent_id' ),
				'booking_id'          => $this->get( 'booking_id' ),
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
	 * {@inheritdoc}
	 */
	protected function perform_update( array $dirty ) {
		global $wpdb;

		$wpdb->update( $wpdb->prefix . 'awebooking_booking_items',
			[
				'booking_id'          => $this->get( 'booking_id' ),
				'booking_item_name'   => $this->get( 'name' ),
				'booking_item_parent' => $this->get( 'parent_id' ),
			],
			[
				'booking_item_id' => $this->get_id(),
			]
		);

		return true;
	}

	/**
	 * {@inheritdoc}
	 */
	protected function perform_delete( $force ) {
		global $wpdb;

		$wpdb->query( $wpdb->prepare( "DELETE FROM `{$wpdb->prefix}awebooking_booking_items` WHERE `booking_item_id` = %d", $this->get_id() ) );
		$wpdb->query( $wpdb->prepare( "DELETE FROM `{$wpdb->prefix}awebooking_booking_itemmeta` WHERE `booking_item_id` = %d", $this->get_id() ) );

		return true;
	}

	/**
	 * {@inheritdoc}
	 */
	protected function sanitize_attribute( $key, $value ) {
		switch ( $key ) {
			case 'parent_id':
			case 'booking_id':
				$value = absint( $value );
				break;
		}

		return apply_filters( $this->prefix( 'sanitize_attribute' ), $value, $key );
	}
}
