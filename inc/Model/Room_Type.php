<?php
namespace AweBooking\Model;

use AweBooking\Constants;
use AweBooking\Model\Pricing\Base_Rate;
use AweBooking\Model\Pricing\Rate_Plan;
use AweBooking\Model\Pricing\Standard_Plan;

class Room_Type extends Model {
	/**
	 * This is the name of this object type.
	 *
	 * @var string
	 */
	protected $object_type = Constants::ROOM_TYPE;

	/**
	 * List the rate plans.
	 *
	 * @var \AweBooking\Support\Collection
	 */
	protected $rate_plans;

	/**
	 * [$services description]
	 *
	 * @var [type]
	 */
	protected $services;

	/**
	 * Gets rooms belongs to this room type.
	 *
	 * @return \AweBooking\Support\Collection
	 */
	public function get_rooms() {
		// If working on non-exists room type, just create an empty rooms.
		$rooms = $this->exists() ? abrs_db_rooms_in( $this->id ) : [];

		$rooms = abrs_collect( $rooms )->map_into( Room::class );

		return apply_filters( $this->prefix( 'get_rooms' ), $rooms, $this );
	}

	/**
	 * Gets rate plans available for this room type.
	 *
	 * @return \AweBooking\Support\Collection
	 */
	public function get_rate_plans() {
		if ( is_null( $this->rate_plans ) ) {
			// Multi rate-plans only available in pro version, please upgrade :).
			$rate_plans = $this->exists()
				? apply_filters( $this->prefix( 'setup_rate_plans' ), [], $this )
				: [];

			$this->rate_plans = abrs_collect( $rate_plans )
				->prepend( $this->get_standard_plan() )
				->filter( function ( $plan ) {
					return $plan instanceof Rate_Plan;
				})->sortBy( function( Rate_Plan $plan ) {
					return $plan->get_priority();
				})->values();
		}

		return $this->rate_plans;
	}

	/**
	 * Returns the standard rate plan of this room type.
	 *
	 * @return \AweBooking\Model\Pricing\Standard_Plan
	 */
	public function get_standard_plan() {
		return apply_filters( $this->prefix( 'get_standard_plan' ), new Standard_Plan( $this ), $this );
	}

	/**
	 * Returns the base rate of this room type.
	 *
	 * @return \AweBooking\Model\Pricing\Standard_Plan
	 */
	public function get_base_rate() {
		return apply_filters( $this->prefix( 'get_base_rate' ), new Base_Rate( $this ), $this );
	}

	public function get_services() {
		dd( $this->get_term_ids( Constants::HOTEL_SERVICE ) );
	}

	/**
	 * {@inheritdoc}
	 */
	protected function clean_cache() {
		$this->rate_plans = null;

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
		$this['hotel_id']          = $this->instance->parent_id;

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
			'hotel_id'     => $this['hotel_id'] ? $this['hotel_id'] : 0,
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
			'hotel_id'      => $this['hotel_id'] ? absint( $this['hotel_id'] ) : 0,
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
			'title'             => '',
			'slug'              => '',
			'status'            => '',
			'date_created'      => null,
			'date_modified'     => null,
			'hotel_id'          => 0,
			'description'       => '',
			'short_description' => '',
			'thumbnail_id'      => 0,
			'gallery_ids'       => [],

			// Room data.
			'maximum_occupancy'   => 0,
			'number_adults'       => 0,
			'number_children'     => 0,
			'number_infants'      => 0,
			'calculation_infants' => 'on', // on | off.

			// Rate.
			'rack_rate'           => 0,
			'rate_inclusions'     => [],
			'rate_policies'       => [],
			'rate_min_los'        => 0,
			'rate_max_los'        => 0,
		]);
	}

	/**
	 * {@inheritdoc}
	 */
	protected function map_attributes() {
		$this->maps = apply_filters( $this->prefix( 'map_attributes' ), [
			'gallery_ids'         => 'gallery',
			'thumbnail_id'        => '_thumbnail_id',

			'maximum_occupancy'   => '_maximum_occupancy',
			'number_adults'       => 'number_adults',
			'number_children'     => 'number_children',
			'number_infants'      => 'number_infants',
			'calculation_infants' => '_infants_in_calculations',

			'rack_rate'           => 'base_price',
			'rate_inclusions'     => '_rate_inclusions',
			'rate_policies'       => '_rate_policies',
			'rate_min_los'        => 'minimum_night',
			'rate_max_los'        => '_rate_maximum_los',
		]);
	}

	/**
	 * {@inheritdoc}
	 */
	protected function sanitize_attribute( $key, $value ) {
		switch ( $key ) {
			case 'gallery_ids':
				$value = is_array( $value ) ? $value : [];
				break;

			case 'rack_rate':
				$value = abrs_sanitize_decimal( $value );
				break;

			case 'description':
			case 'short_description':
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
