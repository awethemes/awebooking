<?php
namespace AweBooking\Model\Traits\Room_Type;

use AweBooking\Support\Carbonate;
use AweBooking\Support\Utils as U;

trait Basic_Attributes_Trait {
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
}
