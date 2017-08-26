<?php
namespace AweBooking;

use AweBooking\Hotel\Room;
use AweBooking\Hotel\Room_Type;
use AweBooking\Booking\Booking;
use AweBooking\Booking\Events\Room_State;
use AweBooking\Booking\Events\Rate_Pricing;
use AweBooking\Booking\Events\Room_Booking;
use Roomify\Bat\Valuator\IntervalValuator;
use AweBooking\Pricing\Rate;
use AweBooking\Pricing\Price;
use AweBooking\Support\Period;

class Concierge {
	/**
	 * Check a room unit is has a state(s).
	 *
	 * This method check only state only, no constraints apply.
	 *
	 * @param  Room         $room_unit The Room instance.
	 * @param  Period       $period    The period.
	 * @param  string|array $states    State(s) to check.
	 * @return bool
	 */
	public static function has_states( Room $room_unit, Period $period, $states, $intersect = true ) {
		$period->required_minimum_nights();

		// Create the availability calendar.
		$calendar = Factory::create_availability_calendar( [ $room_unit ] );

		$response = $calendar->getMatchingUnits(
			$period->get_start_date(), $period->get_end_date()->subMinute(),
			(array) $states, [], $intersect
		);

		return array_key_exists( $room_unit->get_id(),
			$response->getIncluded()
		);
	}

	public static function is_available( Room $room_unit, Period $period ) {
		return static::has_states( $room_unit, $period, AweBooking::STATE_AVAILABLE, false );
	}

	public static function is_unavailable( Room $room_unit, Period $period ) {
		return ! static::is_available( $room_unit, $period );
	}

	/**
	 * Set a room unit availability state.
	 *
	 * @param  Room   $room_unit The room unit.
	 * @param  Period $period    Time period.
	 * @param  int    $state     Oonly AweBooking::STATE_AVAILABLE and AweBooking::STATE_UNAVAILABLE is accepted.
	 * @param  array  $options   Optional, set state options.
	 * @return bool
	 */
	public static function set_availability( Room $room_unit, Period $period, $state, array $options = [] ) {
		$period->required_minimum_nights();

		$room_state = new Room_State( $room_unit, $period, $state );

		// Cannot save a pending or booked state.
		if ( $room_state->is_pending() || $room_state->is_booked() ) {
			return false;
		}

		// If period have any pending or booked state, we cannot to do this.
		if ( static::has_states( $room_unit, $period, [ AweBooking::STATE_PENDING, AweBooking::STATE_BOOKED ] ) ) {
			return false;
		}

		// Only-days only available when set a "availability" state.
		if ( ! empty( $options['only_days'] ) && is_array( $options['only_days'] ) ) {
			$room_state->set_only_days( $options['only_days'] );
		}

		return $room_state->save();
	}

	/**
	 * Set a room unit with a booking and availability state.
	 *
	 * @param  Room    $room_unit Room unit instance.
	 * @param  Period  $period    Time period.
	 * @param  Booking $booking   The booking instance.
	 * @return bool
	 */
	public static function set_booking_state( Room $room_unit, Period $period, Booking $booking ) {
		$period->required_minimum_nights();

		// We only can set if period is available.
		if ( ! static::is_available( $room_unit, $period ) ) {
			return false;
		}

		try {
			// Begin transaction.
			awebooking_wpdb_transaction( 'start' );

			$state_status = (new Room_State( $room_unit, $period, $booking->get_state_status() ))->save();
			$booking_status = (new Room_Booking( $room_unit, $period, $booking ))->save();

			if ( ! $state_status || ! $booking_status ) {
				awebooking_wpdb_transaction( 'rollback' );
				return false;
			}

			// Everything is ok, commit the transaction.
			awebooking_wpdb_transaction( 'commit' );
			return true;
		} catch ( \Exception $e ) {
			awebooking_wpdb_transaction( 'rollback' );
			return false;
		}
	}

	/**
	 * Clear a booking reference to a room unit in a time period.
	 *
	 * @param  Room    $room_unit Room unit instance.
	 * @param  Period  $period    Time period.
	 * @param  Booking $booking   The booking instance.
	 * @return bool
	 */
	public static function clear_booking_state( Room $room_unit, Period $period, Booking $booking ) {
		$period->required_minimum_nights();

		// Check current booking ID of given period.
		$booking_ids = Factory::create_booking_calendar( [ $room_unit ] )->getStates(
			$period->get_start_date(), $period->get_end_date()->subMinute()
		);

		$unit_id = $room_unit->get_id();
		if ( ! isset( $booking_ids[ $unit_id ] ) || count( $booking_ids[ $unit_id ] ) !== 1 ) {
			return false;
		}

		// If given booking is not exists in period, just leave and return false.
		if ( ! array_key_exists( $booking->get_id(), $booking_ids[ $unit_id ] ) ) {
			return false;
		}

		try {
			// Begin transaction.
			awebooking_wpdb_transaction( 'start' );

			// Create an dummy booking with ID 0 to reset.
			$unset_booking  = new Booking( 0 );

			$state_status   = (new Room_State( $room_unit, $period, AweBooking::STATE_AVAILABLE ))->save();
			$booking_status = (new Room_Booking( $room_unit, $period, $unset_booking ))->save();

			if ( ! $state_status || ! $booking_status ) {
				awebooking_wpdb_transaction( 'rollback' );
				return false;
			}

			// Everything is ok, commit the transaction.
			awebooking_wpdb_transaction( 'commit' );
			return true;
		} catch ( \Exception $e ) {
			awebooking_wpdb_transaction( 'rollback' );
			return false;
		}
	}

	/**
	 * Gets room price in a time prioed.
	 *
	 * TODO: ...
	 *
	 * @param  Room_Type $room_type The room type instance.
	 * @param  Period    $period    A time period.
	 * @return Price
	 */
	public static function get_room_price( Room_Type $room_type, Period $period ) {
		$valuator = new IntervalValuator(
			$period->get_start_date(),
			$period->get_end_date()->subMinute(),
			$room_type->get_standard_rate(),
			awebooking()->make( 'store.pricing' ),
			new \DateInterval( 'P1D' )
		);

		return Price::from_amount(
			$valuator->determineValue()
		);
	}

	/**
	 * Set price for room (by rate).
	 *
	 * @param  Rate   $rate    The rate instance.
	 * @param  Period $period  Date period instance.
	 * @param  Price  $amount  The price instance.
	 * @param  array  $options Price setting options.
	 * @return bool
	 */
	public static function set_room_price( Rate $rate, Period $period, Price $amount, array $options = [] ) {
		$rate = new Rate_Pricing( $rate, $period->get_start_date(), $period->get_end_date(), $amount );

		if ( ! empty( $options['only_days'] ) && is_array( $options['only_days'] ) ) {
			$rate->set_only_days( $options['only_days'] );
		}

		return $rate->save();
	}









	/**
	 * Check available.
	 *
	 * @param  Request $request Booking request instance.
	 * @return array
	 */
	public static function check_availability( Request $request ) {
		$room_types = Room_Type::query([
			'booking_adults'   => $request->get_adults(),
			'booking_children' => $request->get_children(),
			'booking_nights'   => $request->get_nights(),
			'hotel_location'   => $request->get_request( 'location' ),
		]);

		$room_types = $room_types->posts;

		$room_ids = wp_list_pluck( $room_types, 'ID' );
		$rooms = Room::get_by_room_type( $room_ids );

		if ( empty( $rooms ) ) {
			return [];
		}

		$rooms = array_map( function( $args ) {
			return new Room( $args['id'] );
		}, $rooms );

		// Temp code, exclude room from request.
		if ( $request->get_request( 'exclude_rooms' ) ) {
			$exclude_rooms = (array) $request->get_request( 'exclude_rooms' );
			$exclude_rooms = array_map( 'absint', $exclude_rooms );

			foreach ( $rooms as $key => $room ) {
				if ( in_array( $room->get_id(), $exclude_rooms ) ) {
					unset( $rooms[ $key ] );
				}
			}
		}

		return $this->check_rooms_available( $rooms, $request );
	}

	/**
	 * Check available a room type.
	 * TODO: ...
	 *
	 * @param  Room_Type         $room_type Room type instance.
	 * @param  Request $request   Booking request instance.
	 * @return Availability
	 */
	public static function check_room_type_availability( Room_Type $room_type, Request $request ) {
		$found = Room_Type::query([
			'booking_adults'   => $request->get_adults(),
			'booking_children' => $request->get_children(),
			'booking_nights'   => $request->get_nights(),
			'post__in'         => [ $room_type->get_id() ],
		]);

		$room_type_array = isset( $found->posts[0] ) ? $found->posts[0] : null;
		if ( empty( $room_type_array ) ) {
			return new Availability( $room_type, $request );
		}

		$rooms = $room_type->get_rooms();
		if ( 0 === count( $rooms ) ) {
			return new Availability( $room_type, $request );
		}

		$available = $this->check_rooms_available( $rooms, $request );

		if ( isset( $available[ $room_type->get_id() ] ) ) {
			return $available[ $room_type->get_id() ];
		}

		return new Availability( $room_type, $request );
	}

	/**
	 * Check available for a list of single rooms.
	 *
	 * @param  array             $rooms   List of single rooms.
	 * @param  Request $request Booking request instance.
	 * @return array
	 */
	public static function check_rooms_available( array $rooms, Request $request ) {
		$calendar = new Calendar( $rooms, awebooking( 'store.availability' ) );

		$response = $calendar->getMatchingUnits(
			$request->get_check_in(),
			$request->get_check_out()->subMinute(),
			$request->valid_states(),
			$request->constraints()
		);

		return $this->mapto_room_types( $response, $request );
	}

	/**
	 * Mapping single-room to that room_type.
	 *
	 * @param  CalendarResponse  $response //.
	 * @param  Request $request //.
	 * @return array
	 */
	protected static function mapto_room_types( $response, Request $request ) {
		$room_type = [];

		foreach ( $response->getIncluded() as $room_id => $accept ) {
			$room_type_id = (int) $accept['unit']->get_room_type()->get_id();

			if ( ! array_key_exists( $room_type_id, $room_type ) ) {
				$room_type[ $room_type_id ] = new Availability( new Room_type( $room_type_id ), $request );
			}

			$room_type[ $room_type_id ]->add_room( $accept['unit'] );
		}

		return $room_type;
	}


	/**
	 * Create new booking.
	 *
	 * @param  array $args The booking arguments.
	 * @return \AweBooking\Booking\Booking
	 */
	public function create_booking( array $args ) {
		$args = wp_parse_args( $args, [
			'status'        => Booking::PENDING,
			'adults'        => 1,
			'children'      => 0,
			'check_in'      => '',
			'check_out'     => '',
			'room_id'       => 0,
			'availability'  => null,

			'customer_id'         => 0,
			'customer_note'       => '',
			'customer_first_name' => '',
			'customer_last_name'  => '',
			'customer_email'      => '',
			'customer_phone'      => '',
			'customer_company'    => '',
		]);

		$insert_id = wp_insert_post([
			'post_status'   => $args['status'],
			'post_type'     => AweBooking::BOOKING,
		], true );

		if ( is_wp_error( $insert_id ) ) {
			return false;
		}

		$booking = new Booking( $insert_id );

		$booking['status']        = $args['status'];
		$booking['adults']        = absint( $args['adults'] );
		$booking['children']      = absint( $args['children'] );
		$booking['check_in']      = $args['check_in'];
		$booking['check_out']     = $args['check_out'];
		$booking['room_id']       = absint( $args['room_id'] );

		$booking['customer_id']         = absint( $args['customer_id'] );
		$booking['customer_note']       = $args['customer_note'];
		$booking['customer_first_name'] = $args['customer_first_name'];
		$booking['customer_last_name']  = $args['customer_last_name'];
		$booking['customer_email']      = $args['customer_email'];
		$booking['customer_phone']      = $args['customer_phone'];
		$booking['customer_company']    = $args['customer_company'];

		if ( $args['availability'] ) {
			$availability = $args['availability'];

			$booking['currency']   = awebooking( 'currency' )->get_code();
			$booking['room_total'] = $availability->get_price()->get_amount();
			$booking['total']      = $availability->get_total_price()->get_amount();

			$booking['request_services'] = $availability->get_request_services();
			$booking['services_total']   = $availability->get_extra_services_price()->get_amount();
		}

		$booking->save();

		return $booking;
	}

}
