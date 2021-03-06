<?php

namespace AweBooking\Admin\Controllers;

use WP_Error;
use WPLibs\Http\Request;
use AweBooking\Admin\Calendar\Booking_Scheduler;

class Calendar_Controller extends Controller {
	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->require_capability( 'manage_awebooking' );
	}

	/**
	 * Show the booking scheduler.
	 *
	 * @param  \WPLibs\Http\Request $request The current request.
	 * @return mixed
	 */
	public function index( Request $request ) {
		$scheduler = new Booking_Scheduler;

		$scheduler->prepare( $request );

		add_action( 'admin_enqueue_scripts', function() {
			$deps = [
				'react',
				'react-dom',
				'wp-api-fetch',
				'wp-components',
				'wp-compose',
				'wp-i18n',
				'wp-url',
				'lodash',
			];

			wp_enqueue_style( 'wp-components' );
			wp_enqueue_style( 'awebooking-scheduler' );
			wp_enqueue_script( 'awebooking-react-scheduler', 'http://localhost:8080/assets/js/scheduler.js', $deps, time(), true );
		});

		return $this->response( 'calendar/index.php', compact( 'scheduler' ) );
	}

	/**
	 * Update state.
	 *
	 * @param \WPLibs\Http\Request $request The current request.
	 * @return mixed
	 */
	public function update( Request $request ) {
		check_admin_referer( 'awebooking_update_state', '_wpnonce' );

		if ( ! $request->filled( 'action', 'room', 'end_date', 'start_date' ) ) {
			return new WP_Error( 'missing_request', esc_html__( 'Hey, you\'re missing some request parameters.', 'awebooking' ) );
		}

		if ( ! $room = abrs_get_room( $request->get( 'room' ) ) ) {
			return new WP_Error( 'room_not_found', esc_html__( 'Sorry, the request room does not exists.', 'awebooking' ) );
		}

		$timespan = abrs_timespan( $request->get( 'start_date' ), $request->get( 'end_date' ), 1 );
		if ( is_wp_error( $timespan ) ) {
			return $timespan;
		}

		switch ( $action = $request->get( 'action', 'unblock' ) ) {
			case 'block':
				$updated = abrs_block_room( $room, $timespan );
				break;

			case 'unblock':
				$updated = abrs_unblock_room( $room, $timespan );
				break;
		}

		return $this->redirect()->back( abrs_admin_route( '/calendar' ) );
	}

	/**
	 * Bulk update state.
	 *
	 * @param \WPLibs\Http\Request $request The current request.
	 * @return mixed
	 */
	public function bulk_update( Request $request ) {
		check_admin_referer( 'awebooking_bulk_update_state', '_wpnonce' );

		if ( ! $request->filled( 'bulk_rooms', 'check-in', 'check-out' ) ) {
			return new WP_Error( 'missing_request', esc_html__( 'Hey, you\'re missing some request parameters.', 'awebooking' ) );
		}

		$timespan = abrs_timespan( $request->get( 'check-in' ), $request->get( 'check-out' ), 1 );
		if ( is_wp_error( $timespan ) ) {
			return $timespan;
		}

		$only_days = $request->get( 'bulk_days' );

		foreach ( (array) $request->get( 'bulk_rooms' ) as $room ) {
			$action = $request->get( 'bulk_action', 'unblock' );

			switch ( $action ) {
				case 'block':
					$updated = abrs_block_room( absint( $room ), $timespan, compact( 'only_days' ) );
					break;

				case 'unblock':
					$updated = abrs_unblock_room( absint( $room ), $timespan, compact( 'only_days' ) );
					break;
			}
		}

		return $this->redirect()->back( abrs_admin_route( '/calendar' ) );
	}
}
