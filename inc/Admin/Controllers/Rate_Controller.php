<?php
namespace AweBooking\Admin\Controllers;

use Awethemes\Http\Request;
use AweBooking\Admin\Forms\Room_Price_Form;
use AweBooking\Admin\Forms\Bulk_Price_Form;
use AweBooking\Admin\Calendar\Pricing_Scheduler;

class Rate_Controller extends Controller {
	/**
	 * Show the pricing scheduler.
	 *
	 * @param  \Awethemes\Http\Request $request The current request.
	 * @return \Awethemes\Http\Response
	 */
	public function index( Request $request ) {
		$controls = new Room_Price_Form;
		$bulk_controls = new Bulk_Price_Form;

		$scheduler = new Pricing_Scheduler;
		$scheduler->prepare( $request );

		return $this->response( 'rates/index.php', compact( 'scheduler', 'controls', 'bulk_controls' ) );
	}

	/**
	 * Show room_type rate.
	 *
	 * @param \Awethemes\Http\Request $request The current request.
	 * @return \Awethemes\Http\Response
	 */
	public function update( Request $request ) {
		check_admin_referer( 'awebooking_update_price', '_wpnonce' );

		// Get the sanitized values.
		$sanitized = ( new Room_Price_Form )->handle( $request );

		if ( $sanitized->count() > 0 && $request->filled( 'calendar', 'check-in', 'check-out' ) ) {
			// Handle set custom room price.
			$updated = abrs_apply_price([
				'rate'       => $request->get( 'calendar' ),
				'room_type'  => $request->get( 'calendar' ),
				'start_date' => $request->get( 'check-in' ),
				'end_date'   => $request->get( 'check-out' ),
				'amount'     => $sanitized->get( 'amount', 0 ),
				'operation'  => $sanitized->get( 'operator', 'replace' ),
				'only_days'  => $sanitized->get( 'days' ),
			]);

			if ( $updated && ! is_wp_error( $updated ) ) {
				abrs_admin_notices( esc_html__( 'Update price successfully', 'awebooking' ), 'success' )->dialog();
			}
		}

		return $this->redirect()->back( abrs_admin_route( '/rates' ) );
	}

	/**
	 * Bulk update rate.
	 *
	 * @param \Awethemes\Http\Request $request The current request.
	 * @return \Awethemes\Http\Response
	 */
	public function bulk_update( Request $request ) {
		check_admin_referer( 'awebooking_bulk_update_price', '_wpnonce' );

		// Get the sanitized values.
		$sanitized = ( new Bulk_Price_Form )->handle( $request );

		$room_types = $request->get( 'bulk_room_types' );

		if ( $sanitized->count() > 0 && $request->filled( 'bulk_room_types', 'check-in', 'check-out' ) ) {

			foreach ( $room_types as $room_type ) {
				// Handle set custom room price.
				$updated = abrs_apply_price([
					'rate'       => $room_type,
					'room_type'  => $room_type,
					'start_date' => $request->get( 'check-in' ),
					'end_date'   => $request->get( 'check-out' ),
					'amount'     => $sanitized->get( 'bulk_amount', 0 ),
					'operation'  => $sanitized->get( 'bulk_operator', 'replace' ),
					'only_days'  => $sanitized->get( 'bulk_days' ),
				]);
			}

			abrs_admin_notices( esc_html__( 'Update price successfully', 'awebooking' ), 'success' )->dialog();
		}

		return $this->redirect()->back( abrs_admin_route( '/rates' ) );
	}
}
