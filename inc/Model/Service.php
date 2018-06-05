<?php
namespace AweBooking\Model;

use AweBooking\Constants;

class Service extends Model {
	const TYPE_OPTIONAL  = 'optional';
	const TYPE_MANDATORY = 'mandatory';

	/**
	 * This is the name of this object type.
	 *
	 * @var string
	 */
	protected $object_type = Constants::HOTEL_SERVICE;

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
		if ( $this->get_changes_only( $dirty, [ 'title', 'status', 'description', 'date_created' ] ) ) {
			$this->update_the_post([
				'post_title'    => $this->get( 'title' ),
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
			'type'          => '',
			'value'         => '',
			'operation'     => '',
		]);
	}

	/**
	 * {@inheritdoc}
	 */
	protected function map_attributes() {
		$this->maps = apply_filters( $this->prefix( 'map_attributes' ), [
			'operation' => '_service_operation',
			'value'     => '_service_value',
			'type'      => '_service_type',
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
