<?php
namespace AweBooking;

use AweBooking\Model\Room;
use AweBooking\Model\Room_Type;
use AweBooking\Model\Booking;
use AweBooking\Booking\Request;
use AweBooking\Booking\Availability;
use AweBooking\Booking\Events\Room_State;
use AweBooking\Booking\Events\Rate_Pricing;
use AweBooking\Booking\Events\Room_Booking;
use Roomify\Bat\Valuator\IntervalValuator;

use AweBooking\Pricing\Rate;
use AweBooking\Money\Price;
use AweBooking\Support\Period;
use AweBooking\Support\Collection;
use Roomify\Bat\Calendar\CalendarResponse;

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
		return static::has_states( $room_unit, $period, Constants::STATE_AVAILABLE, false );
	}

	public static function is_unavailable( Room $room_unit, Period $period ) {
		return ! static::is_available( $room_unit, $period );
	}

	/**
	 * Set a room unit availability state.
	 *
	 * @param  Room   $room_unit The room unit.
	 * @param  Period $period    Time period.
	 * @param  int    $state     Oonly Constants::STATE_AVAILABLE and Constants::STATE_UNAVAILABLE is accepted.
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
		if ( static::has_states( $room_unit, $period, [ Constants::STATE_PENDING, Constants::STATE_BOOKED ] ) ) {
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
	public static function set_booking_state( Room $room_unit, Period $period, Booking $booking, array $options = [] ) {
		$period->required_minimum_nights();

		$options = wp_parse_args( $options, [
			'force' => false,
		]);

		// We only can set if period is available.
		if ( ! $options['force'] && ! static::is_available( $room_unit, $period ) ) {
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

			$state_status   = (new Room_State( $room_unit, $period, Constants::STATE_AVAILABLE ))->save();
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
}
