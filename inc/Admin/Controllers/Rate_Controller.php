<?php

namespace AweBooking\Admin\Controllers;

use AweBooking\Constants;
use WPLibs\Http\Request;
use AweBooking\Admin\Calendar\Pricing_Scheduler;

class Rate_Controller extends Controller {
	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->require_capability( 'manage_awebooking' );
	}

	/**
	 * Show the pricing scheduler.
	 *
	 * @param  \WPLibs\Http\Request $request The current request.
	 * @return \WPLibs\Http\Response
	 */
	public function index( Request $request ) {
		$scheduler_class = apply_filters( 'abrs_pricing_scheduler_class', Pricing_Scheduler::class );

		/* @var \AweBooking\Admin\Calendar\Pricing_Scheduler $scheduler */
		$scheduler = awebooking()->make( $scheduler_class );
		$scheduler->prepare( $request );

		return $this->response( 'rates/index.php', compact( 'scheduler' ) );
	}

	/**
	 * Show room_type rate.
	 *
	 * @param \WPLibs\Http\Request $request The current request.
	 * @return mixed
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

		$updated = abrs_apply_rate( absint( $request->rate ), $timespan, abrs_sanitize_decimal( $request->amount ), $request->operator, [
			'granularity' => Constants::GL_DAILY,
			'only_days'   => $request->get( 'days' ),
		]);

		if ( $updated && ! is_wp_error( $updated ) ) {
			abrs_flash_notices( esc_html__( 'Update price successfully', 'awebooking' ), 'success' )->dialog();
		} elseif ( is_wp_error( $updated ) ) {
			abrs_flash_notices( $updated->get_error_message(), 'error' )->dialog();
		}

		return $this->redirect()->back( abrs_admin_route( '/rates' ) );
	}

	/**
	 * Bulk update rate.
	 *
	 * @param \WPLibs\Http\Request $request The current request.
	 * @return mixed
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
			$updated = abrs_apply_rate( $rate, $timespan, $amount, $request->get( 'bulk_operator' ), [
				'granularity' => Constants::GL_DAILY,
				'only_days'   => $request->get( 'bulk_days' ),
			]);

			if ( $updated && ! is_wp_error( $updated ) ) {
				$bulk_counts++;
			}
		}

		if ( $bulk_counts > 0 ) {
			/* translators: %s: The rates count */
			abrs_flash_notices( sprintf( _n( '%s rate updated.', '%s rates updated.', $bulk_counts, 'awebooking' ), $bulk_counts ), 'success' )->dialog();
		}

		return $this->redirect()->back( abrs_admin_route( '/rates' ) );
	}
}
