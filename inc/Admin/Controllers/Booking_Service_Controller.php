<?php

namespace AweBooking\Admin\Controllers;

use WP_Error;
use WPLibs\Http\Request;
use AweBooking\Model\Service;
use AweBooking\Model\Booking\Service_Item;

class Booking_Service_Controller extends Controller {
	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->require_capability( 'manage_awebooking' );
	}

	/**
	 * Handle create new service.
	 *
	 * @param  \WPLibs\Http\Request $request The current request.
	 * @return mixed
	 */
	public function create( Request $request ) {
		if ( ! $request->filled( 'refer' ) || ! $booking = abrs_get_booking( $request['refer'] ) ) {
			return $this->whoops();
		}

		return $this->response( 'booking/service-form.php', compact( 'booking' ) );
	}

	/**
	 * Handle store new booking service.
	 *
	 * @param  \WPLibs\Http\Request $request The current request.
	 * @return mixed
	 */
	public function store( Request $request ) {
		check_admin_referer( 'create_booking_service', '_wpnonce' );

		if ( ! $request->filled( '_refer' ) || ! $booking = abrs_get_booking( $request['_refer'] ) ) {
			return $this->whoops();
		}

		$included_ids = $booking->get_services()->pluck( 'id' );

		$services = abrs_collect( $request->get( 'services', [] ) );

		// If empty requested services, just clear all.
		if ( $services->isEmpty() ) {
			foreach ( $included_ids as $id ) {
				abrs_delete_booking_item( $id );
			}
		} else {
			$this->handle_sync_services( $booking, $services );
		}

		return $this->redirect()->to( get_edit_post_link( $booking->get_id(), 'raw' ) );
	}


	/**
	 * Delete diff services and add new ones.
	 *
	 * @param \AweBooking\Support\Collection $services The services from request.
	 */
	protected function handle_sync_services( $booking, $services ) {
		$booked_services = $booking->get_services();

		// Filter
		$del = $services->where( 'quantity', '<', 1 )->pluck( 'id' );

		$booked_services
			->whereIn( 'service_id', $del )
			->pluck( 'id' )
			->each( function ( $id ) {
				abrs_delete_booking_item( $id );
			});

		$services = $services->where( 'quantity', '>', 0 );

		// Add & edit service items.
		foreach ( $services as $s ) {
			try {
				$booked_id = $booked_services->where( 'service_id', '=', $s['id'] )->pluck( 'id' )->first();

				$item_id = $booked_id ? $booked_id : 0;

				$service = new Service( $s['id'] );

				$item = new Service_Item( $item_id );

				$item['booking_id'] = $booking->get_id();
				$item['quantity']   = absint( $s['quantity'] );
				$item['price']      = $s['price'];
				$item['total']      = absint( $s['quantity'] ) * $s['price'];

				if ( $service->exists() ) {
					$item['service_id'] = absint( $service->get_id() );
					$item['name']       = esc_html( $service->get_name() );
				}

				$item->save();
			} catch ( \Exception $e ) {
				continue;
			}
		}
	}

	/**
	 * Perform delete a service item.
	 *
	 * @param  \WPLibs\Http\Request                $request      The current request.
	 * @param  \AweBooking\Model\Booking\Service_Item $service_item The booking service item.
	 * @return mixed
	 */
	public function destroy( Request $request, Service_Item $service_item ) {
		check_admin_referer( 'delete_service_' . $service_item->get_id(), '_wpnonce' );

		abrs_delete_booking_item( $service_item );

		abrs_flash_notices( esc_html__( 'The service has been destroyed!', 'awebooking' ), 'info' )->dialog();

		return $this->redirect()->back( get_edit_post_link( $service_item['booking_id'], 'raw' ) );
	}

	/**
	 * Just return a WP_Error for request invalid booking ID.
	 *
	 * @return \WP_Error
	 */
	protected function whoops() {
		return new WP_Error( 404,
			esc_html__( 'You attempted to working with a booking that doesn’t exist. Perhaps it was deleted?', 'awebooking' )
		);
	}
}
