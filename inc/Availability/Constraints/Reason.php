<?php

namespace AweBooking\Availability\Constraints;

class Reason {
	/* Constants */
	const VALID_STATE   = 'valid_state';
	const INVALID_STATE = 'invalid_state';
	const SELECTED_ROOM = 'selected_room';
	const BOOKED_ROOM   = 'booked_room';
	const UNKNOWN       = 'no_reason';

	/**
	 * Get the reason message.
	 *
	 * @param  string $reason The reason key.
	 * @return string
	 */
	public static function get_message( $reason ) {
		$messages = static::get_messages();

		return array_key_exists( $reason, $messages ) ? $messages[ $reason ] : '';
	}

	/**
	 * Get the reason messages.
	 *
	 * @return array
	 */
	public static function get_messages() {
		return [
			static::VALID_STATE   => esc_html__( 'Passed check the available state', 'awebooking' ),
			static::INVALID_STATE => esc_html__( 'Failure check the available state', 'awebooking' ),
			static::SELECTED_ROOM => esc_html__( 'Selected in current session', 'awebooking' ),
			static::BOOKED_ROOM   => esc_html__( 'Booked in current booking', 'awebooking' ),
			static::UNKNOWN       => esc_html__( 'Unknown reason', 'awebooking' ),
		];
	}
}
