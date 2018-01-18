<?php
namespace AweBooking\Deprecated\Model;

use AweBooking\Concierge;
use AweBooking\Booking\Request;
use AweBooking\Pricing\Price_Calculator;
use AweBooking\Model\Room;
use AweBooking\Model\Service;
use AweBooking\Calculator\Service_Calculator;

trait Room_Type_Deprecated {
	public function get_max_adults() {
		return 0;
	}

	public function get_max_children() {
		return 0;
	}

	public function get_max_infants() {
		return 0;
	}

	public function get_allowed_adults() {
		return $this->get_maximum_occupancy();
	}

	public function get_allowed_children() {
		return $this->get_maximum_occupancy();
	}

	public function get_allowed_infants() {
		return $this->get_maximum_occupancy();
	}

	public function get_buyable_identifier( $options ) {
		return $this->get_id();
	}

	public function get_buyable_price( $options ) {
		$options['room-type'] = $this->get_id();
		$request = Request::from_array( $options->to_array() );

		// Price by nights.
		$price = Concierge::get_room_price( $this, $request );
		$pipes = apply_filters( $this->prefix( 'get_buyable_price' ), [], $this, $request );

		if ( $request->has_request( 'extra_services' ) ) {
			foreach ( $request->get_services() as $service_id => $quantity ) {
				$pipes[] = new Service_Calculator( new Service( $service_id ), $request, $price );
			}
		}

		return (new Price_Calculator( $price ))
			->through( $pipes )
			->process();
	}

	public function is_purchasable( $options ) {
		if ( $this->get_base_price()->is_zero() ) {
			return false;
		}

		try {
			$request = Request::from_array( $options->to_array() );
			$availability = Concierge::check_room_type_availability( $this, $request );

			return $availability->available();
		} catch ( \Exception $e ) {
			//
		}
	}

	/**
	 * Bulk sync rooms.
	 *
	 * TODO: Remove late.
	 *
	 * @param  int   $room_type     The room-type ID.
	 * @param  array $request_rooms The request rooms.
	 * @return void
	 */
	public function bulk_sync_rooms( array $request_rooms ) {
		// Current list room of room-type.
		$db_rooms_ids = array_map( 'absint', $this->get_rooms()->pluck( 'id' )->all() );

		// Multilanguage need this.
		$room_type_id = apply_filters( $this->prefix( 'get_id_for_rooms' ), $this->get_id() );

		$touch_ids = [];
		foreach ( $request_rooms as $raw_room ) {
			// Ignore in-valid rooms from request.
			if ( ! isset( $raw_room['id'] ) || ! isset( $raw_room['name'] ) ) {
				continue;
			}

			// Sanitize data before working with database.
			$room_args = array_map( 'sanitize_text_field', $raw_room );

			if ( $room_args['id'] > 0 && in_array( (int) $room_args['id'], $db_rooms_ids ) ) {
				$room_unit = new Room( $room_args['id'] );
				$room_unit['name'] = $room_args['name'];
				$room_unit->save();
			} else {
				$room_unit = new Room;
				$room_unit['name'] = $room_args['name'];
				$room_unit['room_type'] = $room_type_id;
				$room_unit->save();
			}

			// We'll map current working ID in $touch_ids...
			if ( $room_unit->exists() ) {
				$touch_ids[] = $room_unit->get_id();
			}
		}

		// Fimally, delete invisible rooms.
		$delete_ids = array_diff( $db_rooms_ids, $touch_ids );

		if ( ! empty( $delete_ids ) ) {
			global $wpdb;
			$delete_ids = implode( ',', $delete_ids );

			// @codingStandardsIgnoreLine
			$wpdb->query( "DELETE FROM `{$wpdb->prefix}awebooking_rooms` WHERE `id` IN ({$delete_ids})" );
		}
	}
}
