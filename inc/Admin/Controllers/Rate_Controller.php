<?php
namespace AweBooking\Admin\Controllers;

use WP_Error;
use AweBooking\Constants;
use Awethemes\Http\Request;
use AweBooking\Admin\Calendar\Pricing_Scheduler;

class Rate_Controller extends Controller {
	/**
	 * Show the pricing scheduler.
	 *
	 * @param  \Awethemes\Http\Request $request The current request.
	 * @return \Awethemes\Http\Response
	 */
	public function index( Request $request ) {
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

		if ( ! $request->filled( 'rate', 'start_date', 'end_date', 'amount' ) ) {
			return $this->redirect()->back( abrs_admin_route( '/rates' ) );
		}

		$timespan = abrs_timespan( $request->get( 'start_date' ), $request->get( 'end_date' ) );
		if ( is_wp_error( $timespan ) ) {
			return $timespan;
		}

		$updated = abrs_apply_price( absint( $request->rate ), $timespan, abrs_sanitize_decimal( $request->amount ), $request->operator, [
			'granularity' => Constants::GL_DAILY,
			'only_days'   => $request->get( 'days' ),
		]);

		if ( $updated && ! is_wp_error( $updated ) ) {
			abrs_admin_notices( esc_html__( 'Update price successfully', 'awebooking' ), 'success' )->dialog();
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

		if ( ! $request->filled( 'bulk_rates', 'bulk_start_date', 'bulk_end_date', 'bulk_amount' ) ) {
			return $this->redirect()->back( abrs_admin_route( '/rates' ) );
		}

		$timespan = abrs_timespan( $request->get( 'bulk_start_date' ), $request->get( 'bulk_end_date' ) );
		if ( is_wp_error( $timespan ) ) {
			return $timespan;
		}

		// Parse request params.
		$rates  = wp_parse_id_list( $request->bulk_rates );
		$amount = abrs_sanitize_decimal( $request->bulk_amount );

		$bulk_counts = 0;
		foreach ( $rates as $rate ) {
			$updated = abrs_apply_price( $rate, $timespan, $amount, $request->get( 'bulk_operator' ), [
				'granularity' => Constants::GL_DAILY,
				'only_days'   => $request->get( 'bulk_days' ),
			]);

			if ( $updated && ! is_wp_error( $updated ) ) {
				$bulk_counts++;
			}
		}

		if ( $bulk_counts > 0 ) {
			/* translators: %s: The rates count */
			abrs_admin_notices( sprintf( _n( '%s rate updated.', '%s rates updated.', $bulk_counts, 'awebooking' ), $bulk_controls ), 'success' )->dialog();
		}

		return $this->redirect()->back( abrs_admin_route( '/rates' ) );
	}
}
