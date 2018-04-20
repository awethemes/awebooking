<?php
namespace AweBooking\Model;

use Awethemes\WP_Object\WP_Object;

abstract class Model extends WP_Object {
	/**
	 * Prefix for hooks.
	 *
	 * @var string
	 */
	protected $prefix = 'awebooking';

	/**
	 * Constructor.
	 *
	 * @param mixed $object The room-type based object or ID.
	 */
	public function __construct( $object ) {
		$this->setup_attributes();

		$this->map_attributes();

		parent::__construct( $object );
	}

	/**
	 * Get an attribute from this object.
	 *
	 * @param  string $key Attribute key name.
	 * @return mixed|null
	 */
	public function get( $key ) {
		if ( array_key_exists( $key, $this->attributes ) ) {
			return apply_filters( $this->prefix( "get_{$key}" ), $this->get_attribute( $key ), $this );
		}

		trigger_error( sprintf( "Unknown attribute '%s' of %s", esc_html( $key ), esc_html( static::class ) ), E_USER_WARNING );
	}

	/**
	 * Do something before doing save.
	 *
	 * @return void
	 */
	protected function before_save() {
		$call_method = $this->exists() ? 'updating' : 'inserting';

		if ( method_exists( $this, $call_method ) ) {
			call_user_func( [ $this, $call_method ] );
		}
	}

	/**
	 * Setup WP Core Object based on ID and object-type.
	 *
	 * @return void
	 */
	protected function setup_instance() {
		switch ( $this->wp_type ) {
			case 'awebooking_rooms':
				if ( ! is_null( $room = abrs_db_room( $this->id ) ) ) {
					$this->set_instance( $room );
				}
				break;

			case 'awebooking_item':
				if ( ! is_null( $booking_item = abrs_db_booking_item( $this->id ) ) ) {
					$this->set_instance( $booking_item );
				}
				break;

			default:
				parent::setup_instance();
				break;
		}
	}

	/**
	 * Clean object cache after saved.
	 *
	 * @return void
	 */
	protected function clean_cache() {
		switch ( $this->wp_type ) {
			case 'post':
				clean_post_cache( $this->id );
				break;

			case 'awebooking_rooms':
				abrs_clean_room_cache( $this->id );
				break;

			case 'awebooking_item':
				abrs_clean_booking_item_cache( $this->id );
				break;
		}
	}

	/**
	 * Setup the attributes.
	 *
	 * @return void
	 */
	protected function setup_attributes() {}

	/**
	 * Setup map meta data with attributes.
	 *
	 * @return void
	 */
	protected function map_attributes() {}

	/**
	 * Handle dynamic calls to get attributes.
	 *
	 * @param  string $method     The method name.
	 * @param  array  $parameters The method parameters.
	 * @return $this
	 *
	 * @throws \BadMethodCallException
	 */
	public function __call( $method, $parameters ) {
		if ( 0 === strpos( $method, 'get_' ) ) {
			return $this->get( substr( $method, 4 ) );
		}

		throw new \BadMethodCallException( sprintf( 'Method %s::%s does not exist.', static::class, $method ) );
	}
}
