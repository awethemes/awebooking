<?php
namespace AweBooking\Model\Concerns;

use AweBooking\Support\Carbonate;
use AweBooking\Support\Utils as U;

trait Room_Type_Attributes {
	/**
	 * Get the title.
	 *
	 * @return string
	 */
	public function get_title() {
		return apply_filters( $this->prefix( 'get_title' ), $this['title'], $this );
	}

	/**
	 * Get the slug.
	 *
	 * @return string
	 */
	public function get_slug() {
		return apply_filters( $this->prefix( 'get_slug' ), $this['slug'], $this );
	}

	/**
	 * Get the status.
	 *
	 * @return string
	 */
	public function get_status() {
		return apply_filters( $this->prefix( 'get_status' ), $this['status'], $this );
	}

	/**
	 * Get the description.
	 *
	 * @return string
	 */
	public function get_description() {
		return apply_filters( $this->prefix( 'get_description' ), $this['description'], $this );
	}

	/**
	 * Get the short description.
	 *
	 * @return string
	 */
	public function get_short_description() {
		return apply_filters( $this->prefix( 'get_short_description' ), $this['short_description'], $this );
	}

	/**
	 * Get the thumbnail ID.
	 *
	 * @return int
	 */
	public function get_thumbnail_id() {
		return apply_filters( $this->prefix( 'get_thumbnail_id' ), $this['thumbnail_id'], $this );
	}

	/**
	 * Get the gallery IDs.
	 *
	 * @return array int[]
	 */
	public function get_gallery_ids() {
		return apply_filters( $this->prefix( 'get_gallery_ids' ), $this['gallery_ids'], $this );
	}

	/**
	 * Get room_type created date.
	 *
	 * @return \AweBooking\Support\Carbonate|null
	 */
	public function get_date_created() {
		if ( empty( $this['date_created'] ) ) {
			return;
		}

		$date_created = Utils::rescue( function() {
			return Carbonate::create_datetime( $this['date_created'] );
		});

		return apply_filters( $this->prefix( 'get_date_created' ), $date_created, $this );
	}

	/**
	 * Get room_type modified date.
	 *
	 * @return \AweBooking\Support\Carbonate|null
	 */
	public function get_date_modified() {
		if ( empty( $this['date_modified'] ) ) {
			return;
		}

		$date_modified = Utils::rescue( function() {
			return Carbonate::create_datetime( $this['date_modified'] );
		});

		return apply_filters( $this->prefix( 'get_date_modified' ), $date_modified, $this );
	}

	/**
	 * Get the maximum occupancy.
	 *
	 * @return int
	 */
	public function get_maximum_occupancy() {
		return apply_filters( $this->prefix( 'get_maximum_occupancy' ), $this['maximum_occupancy'], $this );
	}

	/**
	 * Set the maximum occupancy.
	 *
	 * @param  int $value The number value.
	 * @return $this
	 */
	public function set_maximum_occupancy( $value ) {
		$this->attributes['maximum_occupancy'] = max( absint( $value ), 1 );

		return $this;
	}

	/**
	 * Get number adults allowed.
	 *
	 * @return int
	 */
	public function get_number_adults() {
		return apply_filters( $this->prefix( 'get_number_adults' ), $this['number_adults'], $this );
	}

	/**
	 * Set the number adults.
	 *
	 * @param  int $number_adults The number value.
	 * @return $this
	 */
	public function set_number_adults( $number_adults ) {
		$this->attributes['number_adults'] = $this->fillter_occupancy_number( $number_adults );

		return $this;
	}

	/**
	 * Get number children allowed.
	 *
	 * @return int
	 */
	public function get_number_children() {
		return apply_filters( $this->prefix( 'get_number_children' ), $this['number_children'], $this );
	}

	/**
	 * Set the number children.
	 *
	 * @param  int $number_children The number value.
	 * @return $this
	 */
	public function set_number_children( $number_children ) {
		$this->attributes['number_children'] = $this->fillter_occupancy_number( $number_children );

		return $this;
	}

	/**
	 * Get number children allowed.
	 *
	 * @return int
	 */
	public function get_number_infants() {
		return apply_filters( $this->prefix( 'get_number_infants' ), $this['number_infants'], $this );
	}

	/**
	 * Set the number infants.
	 *
	 * @param  int $number_infants The number value.
	 * @return $this
	 */
	public function set_number_infants( $number_infants ) {
		$this->attributes['number_infants'] = $this->fillter_occupancy_number( $number_infants );

		return $this;
	}

	/**
	 * Determines is this room_type include infants in max calculations?
	 *
	 * @return boolean
	 */
	public function is_calculation_infants() {
		return apply_filters( $this->prefix( 'is_calculation_infants' ), $this['calculation_infants'], $this );
	}

	/**
	 * Filter the number occupancy value.
	 *
	 * @param  mixed $value Input data.
	 * @return int
	 */
	protected function fillter_occupancy_number( $value ) {
		if ( ! is_numeric( $value ) ) {
			return 1;
		}

		return min( absint( $value ), (int) $this->get_maximum_occupancy() );
	}
}
