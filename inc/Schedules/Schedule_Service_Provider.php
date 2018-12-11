<?php

namespace AweBooking\Schedules;

use AweBooking\Support\Service_Provider;

class Schedule_Service_Provider extends Service_Provider {
	/**
	 * Init (boot) the service provider.
	 *
	 * @return void
	 */
	public function init() {
		add_action( 'abrs_booking_status_changed', [ $this, 'handle_status_changes' ], 10, 3 );
		add_action( 'abrs_schedule_update_checkout_status', [ $this, 'schedule_update_checkout_status' ] );

		add_action( 'abrs_checkout_processed', [ $this, 'handle_checkout_processed' ] );
		add_action( 'abrs_schedule_clean_booking', [ $this, 'schedule_clean_booking' ] );
	}

	/**
	 * Handle status changes.
	 *
	 * @param string                    $new_status The new status.
	 * @param string                    $old_status The old status.
	 * @param \AweBooking\Model\Booking $booking    The booking instance.
	 *
	 * @return void
	 */
	public function handle_status_changes( $new_status, $old_status, $booking ) {
		$old_status = ( 0 === strpos( $old_status, 'awebooking-' ) ) ? substr( $old_status, 11 ) : $old_status;

		if ( ! $check_out = $booking->get_check_out_date() ) {
			return;
		}

		$args = [ $booking->get_id() ];

		$schedule_date = abrs_date( $check_out )->endOfDay();

		// Modify run time at the time hotel checkout.
		$checkout_time = abrs_get_option( 'hotel_check_out' );

		if ( $checkout_time && preg_match( '/^([0-9]|0[0-9]|1[0-9]|2[0-3]):([0-5][0-9])$/', $checkout_time, $matches ) ) {
			$schedule_date = $schedule_date->hour( $matches[1] )->minute( $matches[2] )->second( 0 );
		}

		if ( 'checked-in' === $new_status && in_array( $old_status, [ 'on-hold', 'inprocess', 'completed', 'deposit' ] ) ) {
			wp_schedule_single_event( $schedule_date->timestamp, 'abrs_schedule_update_checkout_status', $args );

			return;
		}

		if ( wp_next_scheduled( 'abrs_schedule_update_checkout_status', $args ) ) {
			wp_unschedule_event( $schedule_date->timestamp, 'abrs_schedule_update_checkout_status', $args );
		}
	}

	/**
	 * Update checkout status.
	 *
	 * @param int $booking_id The booking ID.
	 */
	public function schedule_update_checkout_status( $booking_id ) {
		$booking = abrs_get_booking( $booking_id );

		if ( $booking && 'checked-out' !== $booking->get_status() ) {
			$booking->update_status( 'checked-out', esc_html__( 'Cron: Auto update status to checked-out.', 'awebooking' ) );
		}
	}

	/**
	 * Handle checkout processed after 30 minutes.
	 *
	 * @param int $booking_id Booking ID.
	 */
	public function handle_checkout_processed( $booking_id ) {
		$booking = abrs_get_booking( $booking_id );

		$date = abrs_date_time( 'now' )->addMinutes( 30 );
		$args = [ $booking->get_id() ];

		if ( wp_next_scheduled( 'abrs_schedule_clean_booking', $args ) ) {
			wp_unschedule_event( $date->timestamp, 'abrs_schedule_clean_booking', $args );
		}

		wp_schedule_single_event( $date->timestamp, 'abrs_schedule_clean_booking', $args );
	}

	/**
	 * Clean booking when booking status is `pending`.
	 *
	 * @param int $booking_id The booking ID.
	 */
	public function schedule_clean_booking( $booking_id ) {
		$booking = abrs_get_booking( $booking_id );

		if ( $booking && 'pending' === $booking->get_status() ) {
			$booking->delete( true );
		}
	}
}
