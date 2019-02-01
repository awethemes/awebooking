<?php

namespace AweBooking\Model;

use AweBooking\Constants;

class Room_Type extends Model {
	/**
	 * This is the name of this object type.
	 *
	 * @var string
	 */
	protected $object_type = Constants::ROOM_TYPE;

	/**
	 * List the rates.
	 *
	 * @var \AweBooking\Support\Collection
	 */
	protected $rates;

	/**
	 * Gets the gallery IDs.
	 *
	 * @return array|mixed
	 */
	public function get_gallery_ids() {
		$gallery_ids = $this['gallery_ids'];

		if ( $gallery_ids && ! isset( $gallery_ids[0] ) ) {
			$gallery_ids = array_keys( $gallery_ids );
		}

		return $gallery_ids;
	}

	/**
	 * Gets rooms belongs to this room type.
	 *
	 * @return \AweBooking\Support\Collection
	 */
	public function get_rooms() {
		// If working on non-exists room type, just create an empty rooms.
		$rooms = $this->exists() ? abrs_get_raw_rooms( $this->id ) : [];

		$rooms = abrs_collect( $rooms )->map_into( Room::class );

		return apply_filters( $this->prefix( 'get_rooms' ), $rooms, $this );
	}

	/**
	 * Gets rates available for this room type.
	 *
	 * @return \AweBooking\Support\Collection
	 */
	public function get_rates() {
		if ( ! $this->exists() ) {
			return abrs_collect( [] );
		}

		if ( is_null( $this->rates ) ) {
			$this->rates = abrs_query_rates( $this )
				->push( abrs_get_base_rate( $this ) );
		}

		return $this->rates;
	}

	/**
	 * {@inheritdoc}
	 */
	protected function clean_cache() {
		$this->rates = null;

		clean_post_cache( $this->get_id() );

		wp_cache_delete( $this->get_id(), 'awebooking_rooms' );
	}

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
		]);

		// Allow continue save meta-data if nothing to update post.
		return true;
	}

	/**
	 * {@inheritdoc}
	 */
	protected function setup_attributes() {
		$this->attributes = apply_filters( $this->prefix( 'attributes' ), [
			// Basic infomations.
			'title'                             => '',
			'slug'                              => '',
			'status'                            => '',
			'date_created'                      => null,
			'date_modified'                     => null,
			'hotel_id'                          => 0,
			'description'                       => '',
			'short_description'                 => '',
			'thumbnail_id'                      => 0,
			'gallery_ids'                       => [],

			// Room data.
			'bedrooms'                          => 0,
			'beds'                              => [],
			'view'                              => '',
			'area_size'                         => '',
			'maximum_occupancy'                 => 0,
			'number_adults'                     => 0,
			'number_children'                   => 0,
			'number_infants'                    => 0,
			'calculation_infants'               => 'on', // on | off.
			'tax_rate_id'                       => 0,
			'availability_allowed_checkin_days' => [],
			'availability_period_bookable'      => '',

			// Rate.
			'rack_rate'                         => 0,
			'rate_services'                     => [],
			'rate_inclusions'                   => [],
			'rate_policies'                     => [],
			'rate_min_los'                      => 0,
			'rate_max_los'                      => 0,
		]);
	}

	/**
	 * {@inheritdoc}
	 */
	protected function map_attributes() {
		$this->maps = apply_filters( $this->prefix( 'map_attributes' ), [
			'gallery_ids'                       => 'gallery',
			'thumbnail_id'                      => '_thumbnail_id',
			'hotel_id'                          => '_hotel_id',
			'bedrooms'                          => '_bedrooms',

			'beds'                              => '_beds',
			'view'                              => '_room_view',
			'area_size'                         => '_area_size',
			'maximum_occupancy'                 => '_maximum_occupancy',
			'number_adults'                     => 'number_adults',
			'number_children'                   => 'number_children',
			'number_infants'                    => 'number_infants',
			'calculation_infants'               => '_infants_in_calculations',
			'tax_rate_id'                       => '_tax_rate_id',
			'availability_allowed_checkin_days' => '_availability_allowed_checkin_days',
			'availability_period_bookable'      => '_availability_period_bookable',

			'rack_rate'                         => 'base_price',
			'rate_services'                     => '_rate_services',
			'rate_inclusions'                   => '_rate_inclusions',
			'rate_policies'                     => '_rate_policies',
			'rate_min_los'                      => 'minimum_night',
			'rate_max_los'                      => '_rate_maximum_los',
		]);
	}

	/**
	 * {@inheritdoc}
	 */
	protected function sanitize_attribute( $key, $value ) {
		switch ( $key ) {
			case 'beds':
			case 'gallery_ids':
			case 'rate_services':
				$value = is_array( $value ) ? $value : [];
				break;

			case 'rack_rate':
				$value = abrs_sanitize_decimal( $value );
				break;

			case 'description':
			case 'short_description':
			case 'view':
			case 'area_size':
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
			case 'bedrooms':
			case 'maximum_occupancy':
			case 'number_adults':
			case 'number_children':
			case 'number_infants':
			case 'rate_min_los':
			case 'rate_max_los':
			case 'hotel_id':
			case 'tax_rate_id':
				$value = absint( $value );
				break;
		}

		return apply_filters( $this->prefix( 'sanitize_attribute' ), $value, $key );
	}
}
