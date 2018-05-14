<?php
namespace AweBooking\Model;

use AweBooking\Constants;

class Hotel extends Model {
	/**
	 * This is the name of this object type.
	 *
	 * @var string
	 */
	protected $object_type = Constants::HOTEL_LOCATION;

	/**
	 * {@inheritdoc}
	 */
	protected function clean_cache() {
		clean_post_cache( $this->get_id() );
	}

	/**
	 * {@inheritdoc}
	 */
	protected function setup() {
		$this['name']              = $this->instance->post_title;
		$this['slug']              = $this->instance->post_name;
		$this['status']            = $this->instance->post_status;
		$this['order']             = $this->instance->menu_order;
		$this['description']       = $this->instance->post_content;
		$this['date_created']      = $this->instance->post_date;
		$this['date_modified']     = $this->instance->post_modified;

		do_action( $this->prefix( 'after_setup' ), $this );
	}

	/**
	 * {@inheritdoc}
	 */
	protected function perform_insert() {
		$insert_id = wp_insert_post([
			'post_type'    => $this->object_type,
			'post_title'   => $this['name'],
			'menu_order'   => $this->get( 'order' ),
			'post_content' => $this['description'],
			'post_status'  => $this['status'] ? $this['status'] : 'publish',
			'post_date'    => $this['post_date'] ? $this['post_date'] : current_time( 'mysql' ),
		], true );

		if ( ! is_wp_error( $insert_id ) ) {
			return $insert_id;
		}
	}

	/**
	 * {@inheritdoc}
	 */
	protected function perform_update( array $dirty ) {
		if ( $this->get_changes_only( $dirty, [ 'name', 'status', 'order', 'description', 'date_created', 'date_modified' ] ) ) {
			$this->update_the_post([
				'post_title'    => $this['name'],
				'post_status'   => $this['status'],
				'menu_order'    => $this['order'],
				'post_content'  => $this['description'],
				'post_date'     => $this['date_created'] ? (string) abrs_date_time( $this['date_created'] ) : '',
				'post_modified' => $this['date_modified'] ? (string) abrs_date_time( $this['date_modified'] ) : '',
			]);
		}
	}

	/**
	 * {@inheritdoc}
	 */
	protected function setup_attributes() {
		$this->attributes = apply_filters( $this->prefix( 'attributes' ), [
			'name'              => '',
			'slug'              => '',
			'status'            => '',
			'order'             => 0,
			'date_created'      => null,
			'date_modified'     => null,
			'description'       => '',
			'star_rating'       => '',
			'hotel_address'     => '',
			'hotel_address_2'   => '',
			'hotel_state'       => '',
			'hotel_city'        => '',
			'hotel_country'     => '',
			'hotel_postcode'    => '',
			'hotel_phone'       => '',
		]);
	}

	/**
	 * {@inheritdoc}
	 */
	protected function map_attributes() {
		$this->maps = apply_filters( $this->prefix( 'map_attributes' ), [
			'star_rating'       => '_star_rating',
			'hotel_address'     => '_hotel_address',
			'hotel_address_2'   => '_hotel_address_2',
			'hotel_state'       => '_hotel_state',
			'hotel_city'        => '_hotel_city',
			'hotel_country'     => '_hotel_country',
			'hotel_postcode'    => '_hotel_postcode',
			'hotel_phone'       => '_hotel_phone',
		]);
	}

	/**
	 * {@inheritdoc}
	 */
	protected function sanitize_attribute( $key, $value ) {
		return apply_filters( $this->prefix( 'sanitize_attribute' ), $value, $key );
	}
}
