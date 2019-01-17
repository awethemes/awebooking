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
	 * Get all service stock statuses.
	 *
	 * @return array
	 */
	public static function get_stock_statuses() {
		return apply_filters( 'abrs_get_service_stock_statuses', [
			'instock'     => esc_html__( 'In stock', 'awebooking' ),
			'outofstock'  => esc_html__( 'Out of stock', 'awebooking' ),
		]);
	}

	/**
	 * Gets the service amount.
	 *
	 * @return float
	 */
	public function get_amount() {
		return $this->get( 'amount' );
	}

	/**
	 * Gets the service operation.
	 *
	 * @return string
	 */
	public function get_operation() {
		return $this->get( 'operation' );
	}

	/**
	 * Is the quantity selectable?
	 *
	 * @return bool
	 */
	public function is_quantity_selectable() {
		return 'on' === abrs_sanitize_checkbox( $this->get( 'quantity_selectable' ) );
	}

	/**
	 * Returns false if the service cannot be bought.
	 *
	 * @return bool
	 */
	public function is_purchasable() {
		return apply_filters( $this->prefix( 'is_purchasable' ),
			$this->exists() && ( 'publish' === $this->get( 'status' ) || current_user_can( 'edit_post', $this->get_id() ) ),
			$this
		);
	}

	/**
	 * Returns whether or not the service can be purchased.
	 *
	 * This returns true for 'instock' status.
	 *
	 * @return bool
	 */
	public function is_in_stock() {
		return apply_filters( $this->prefix( 'is_in_stock' ), 'outofstock' !== $this->get( 'stock_status' ), $this );
	}

	/**
	 * Returns whether or not the service is stock managed.
	 *
	 * @return bool
	 */
	public function managing_stock() {
		return $this->get( 'manage_stock' );
	}

	/**
	 * Returns whether or not the service has enough stock for the reservation.
	 *
	 * @param  mixed $quantity Quantity of a service added to an reservation.
	 * @return bool
	 */
	public function has_enough_stock( $quantity ) {
		return ! $this->managing_stock() || $this->get( 'stock_quantity' ) >= $quantity;
	}

	/**
	 * {@inheritdoc}
	 */
	protected function setup() {
		$this->set_attribute( 'name', $this->instance->post_title );
		$this->set_attribute( 'description', $this->instance->post_excerpt );
		$this->set_attribute( 'status', $this->instance->post_status );
		$this->set_attribute( 'date_created', $this->instance->post_date );
		$this->set_attribute( 'date_modified', $this->instance->post_modified );

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
			'status'              => '',
			'date_created'        => null,
			'date_modified'       => null,
			'thumbnail_id'        => 0,
			'icon'                => [],
			'name'                => '',
			'description'         => '',
			'amount'              => 0,
			'operation'           => '',
			'quantity_selectable' => false,
			'stock_status'        => 'instock',
			'manage_stock'        => false,
			'stock_quantity'      => 0,
		]);
	}

	/**
	 * {@inheritdoc}
	 */
	protected function map_attributes() {
		$this->maps = apply_filters( $this->prefix( 'map_attributes' ), [
			'operation'           => '_operation',
			'amount'              => '_amount',
			'thumbnail_id'        => '_thumbnail_id',
			'icon'                => '_icon',
			'quantity_selectable' => '_quantity_selectable',
			'stock_status'        => '_stock_status',
			'manage_stock'        => '_manage_stock',
			'stock_quantity'      => '_stock_quantity',
		]);
	}

	/**
	 * {@inheritdoc}
	 */
	protected function sanitize_attribute( $key, $value ) {
		switch ( $key ) {
			case 'amount':
				$value = abrs_sanitize_decimal( $value );
				break;
		}

		return apply_filters( $this->prefix( 'sanitize_attribute' ), $value, $key );
	}
}
