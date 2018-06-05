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
		$this['title']             = $this->instance->post_title;
		$this['slug']              = $this->instance->post_name;
		$this['status']            = $this->instance->post_status;
		$this['description']       = $this->instance->post_content;
		$this['short_description'] = $this->instance->post_excerpt;
		$this['date_created']      = $this->instance->post_date;
		$this['date_modified']     = $this->instance->post_modified;
		$this['hotel_id']          = $this->instance->post_parent;

		// Correct the gallery_ids.
		if ( $this['gallery_ids'] && ! isset( $this['gallery_ids'][0] ) ) {
			$this['gallery_ids'] = array_keys( $this['gallery_ids'] );
		}

		do_action( $this->prefix( 'after_setup' ), $this );
	}

	/**
	 * {@inheritdoc}
	 */
	protected function perform_insert() {
		$insert_id = wp_insert_post([
			'post_type'    => $this->object_type,
			'post_title'   => $this['title'],
			'post_content' => $this['description'],
			'post_excerpt' => $this['short_description'],
			'post_status'  => $this['status'] ? $this['status'] : 'publish',
			'post_date'    => $this['post_date'] ? $this['post_date'] : current_time( 'mysql' ),
			'post_parent'  => $this['hotel_id'] ? $this['hotel_id'] : 0,
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
		$this->update_the_post([
			'post_title'    => $this['title'],
			'post_status'   => $this['status'],
			'post_content'  => $this['description'],
			'post_excerpt'  => $this['short_description'],
			'post_date'     => $this['date_created'] ? (string) abrs_date_time( $this['date_created'] ) : '',
			'post_modified' => $this['date_modified'] ? (string) abrs_date_time( $this['date_modified'] ) : '',
			'post_parent'   => $this['hotel_id'] ? absint( $this['hotel_id'] ) : 0,
		]);

		// Allow continue save meta-data if nothing to update post.
		return true;
	}

	/**
	 * {@inheritdoc}
	 */
	protected function setup_attributes() {
		$this->attributes = apply_filters( $this->prefix( 'attributes' ), [
			'status'            => '',
			'name'              => '',
			'description'       => '',
			'type'              => '',
			'operation'         => '',
			'value'             => '',
			'date_created'      => null,
			'date_modified'     => null,
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
			case 'gallery_ids':
			case 'beds':
				$value = is_array( $value ) ? $value : [];
				break;

			case 'rack_rate':
				$value = abrs_sanitize_decimal( $value );
				break;

			case 'description':
			case 'short_description':
			case 'view':
				$value = abrs_sanitize_html( $value );
				break;

			case 'rate_policies':
			case 'rate_inclusions':
				if ( $value && is_string( $value ) ) {
					$value = abrs_clean( explode( "\n", $value ) );
				}

				$value = is_array( $value ) ? array_filter( $value ) : [];
				break;

			case 'thumbnail_id':
			case 'maximum_occupancy':
			case 'number_adults':
			case 'number_children':
			case 'number_infants':
			case 'rate_min_los':
			case 'rate_max_los':
			case 'hotel_id':
				$value = absint( $value );
				break;
		}

		return apply_filters( $this->prefix( 'sanitize_attribute' ), $value, $key );
	}
}
