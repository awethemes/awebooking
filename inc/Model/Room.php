<?php

namespace AweBooking\Model;

class Room extends Model {
	/**
	 * Name of object type.
	 *
	 * @var string
	 */
	protected $object_type = 'room';

	/**
	 * WordPress type for object.
	 *
	 * @var string
	 */
	protected $wp_type = 'awebooking_rooms';

	/**
	 * This object does not support metadata.
	 *
	 * @var false
	 */
	protected $meta_type = false;

	/**
	 * The attributes for this object.
	 *
	 * @var array
	 */
	protected $attributes = [
		'name'      => '',
		'order'     => 0,
		'room_type' => 0,
	];

	/**
	 * Constructor.
	 *
	 * @param int|array $object The room ID or array room data.
	 */
	public function __construct( $object = 0 ) {
		if ( is_array( $object ) ) {
			$this->setup_from_array( $object );
		} else {
			parent::__construct( $object );
		}
	}

	/**
	 * Setup object from an array.
	 *
	 * @param  array $data The data.
	 * @return void
	 */
	protected function setup_from_array( array $data ) {
		// Prevent setup from invalid data.
		if ( ! isset( $data['id'], $data['room_type'] ) ) {
			return;
		}

		$this->id = absint( $data['id'] );
		$this->exists = true;
		$this->set_instance( $data );

		$this->setup();
		$this->sync_original();
	}

	/**
	 * {@inheritdoc}
	 */
	protected function setup() {
		$this['name']      = $this->instance['name'];
		$this['order']     = absint( $this->instance['order'] );
		$this['room_type'] = absint( $this->instance['room_type'] );
	}

	/**
	 * Clean object cache after saved.
	 *
	 * @return void
	 */
	protected function clean_cache() {
		abrs_flush_room_cache( $this->id );

		wp_cache_delete( (int) $this->get( 'room_type' ), 'awebooking_rooms' );
	}

	/**
	 * {@inheritdoc}
	 */
	protected function perform_insert() {
		global $wpdb;

		// We need a room-type attribute present.
		if ( empty( $this->attributes['room_type'] ) ) {
			return 0;
		}

		$wpdb->insert( $wpdb->prefix . 'awebooking_rooms',
			$this->only( 'name', 'room_type', 'order' ),
			[ '%s', '%d', '%d' ]
		);

		return absint( $wpdb->insert_id );
	}

	/**
	 * {@inheritdoc}
	 */
	protected function perform_update( array $dirty ) {
		global $wpdb;

		// We need a room-type present for the update.
		if ( empty( $this->attributes['room_type'] ) ) {
			return;
		}

		$updated = $wpdb->update( $wpdb->prefix . 'awebooking_rooms',
			$this->only( 'name', 'room_type', 'order' ),
			[ 'id' => $this->get_id() ]
		);

		return false !== $updated;
	}

	/**
	 * {@inheritdoc}
	 */
	protected function perform_delete( $force ) {
		global $wpdb;

		$deleted = $wpdb->delete( $wpdb->prefix . 'awebooking_rooms', [ 'id' => $this->get_id() ], '%d' );

		return false !== $deleted;
	}

	/**
	 * {@inheritdoc}
	 */
	protected function sanitize_attribute( $key, $value ) {
		switch ( $key ) {
			case 'order':
			case 'room_type':
				$value = absint( $value );
				break;
		}

		return $value;
	}
}
