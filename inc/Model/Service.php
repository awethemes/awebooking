<?php
namespace AweBooking\Model;

use AweBooking\Constants;

class Service extends Model {

	/**
	 * This is the name of this object type.
	 *
	 * @var string
	 */
	protected $object_type = Constants::HOTEL_SERVICE;

	/**
	 * Get all service operations.
	 *
	 * @return array
	 */
	public static function get_operations() {
		return apply_filters( 'abrs_get_service_operations', [
			'add'               => esc_html__( 'Add to price', 'awebooking' ),
			'add_daily'         => esc_html__( 'Add to price per night', 'awebooking' ),
			'add_person'        => esc_html__( 'Add to price per person', 'awebooking' ),
			'add_person_daily'  => esc_html__( 'Add to price per person per night', 'awebooking' ),
			'sub'               => esc_html__( 'Subtract from price', 'awebooking' ),
			'sub_daily'         => esc_html__( 'Subtract from price per night', 'awebooking' ),
			'increase'          => esc_html__( 'Increase price by % amount', 'awebooking' ),
			'decrease'          => esc_html__( 'Decrease price by % amount', 'awebooking' ),
		]);
	}

	/**
	 * {@inheritdoc}
	 */
	protected function setup() {
		$this->set_attribute( 'name', $this->instance->post_title );
		$this->set_attribute( 'description', $this->instance->post_excerpt );
		$this->set_attribute( 'date_created', $this->instance->post_date );
		$this->set_attribute( 'date_modified', $this->instance->post_modified );
		$this->set_attribute( 'status', $this->instance->post_status );

		do_action( $this->prefix( 'after_setup' ), $this );
	}

	/**
	 * {@inheritdoc}
	 */
	protected function perform_insert() {
		$insert_id = wp_insert_post([
			'post_type'    => $this->object_type,
			'post_title'   => $this->get( 'name' ),
			'post_excerpt' => $this->get( 'description' ),
			'post_status'  => $this->get( 'status' ) ?: 'publish',
			'post_date'    => $this->get( 'date_created' ) ?: current_time( 'mysql' ),
		], true );

		if ( ! is_wp_error( $insert_id ) ) {
			return $insert_id;
		}

		return 0;
	}

	/**
	 * {@inheritdoc}
	 */
	protected function perform_update( array $dirty ) {
		if ( $this->get_changes_only( $dirty, [ 'name', 'status', 'description', 'date_created' ] ) ) {
			$this->update_the_post([
				'post_title'    => $this->get( 'name' ),
				'post_status'   => $this->get( 'status' ),
				'post_excerpt'  => $this->get( 'description' ),
				'post_date'     => $this->get( 'date_created' ),
			]);
		}
	}

	/**
	 * {@inheritdoc}
	 */
	protected function setup_attributes() {
		$this->attributes = apply_filters( $this->prefix( 'attributes' ), [
			'name'          => '',
			'description'   => '',
			'date_created'  => null,
			'date_modified' => null,
			'status'        => '',
			'value'         => '',
			'operation'     => '',
			'icon'          => [],
		]);
	}

	/**
	 * {@inheritdoc}
	 */
	protected function map_attributes() {
		$this->maps = apply_filters( $this->prefix( 'map_attributes' ), [
			'operation' => '_operation',
			'value'     => '_value',
			'icon'      => '_icon',
		]);
	}

	/**
	 * {@inheritdoc}
	 */
	protected function sanitize_attribute( $key, $value ) {
		switch ( $key ) {
			case 'value':
				$value = abrs_sanitize_decimal( $value );
				break;
		}

		return apply_filters( $this->prefix( 'sanitize_attribute' ), $value, $key );
	}
}
