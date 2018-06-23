<?php
namespace AweBooking\Admin\Controllers;

use WP_Error;
use Awethemes\Http\Request;
use AweBooking\Model\Service;
use AweBooking\Model\Booking\Service_Item;
use AweBooking\Admin\Forms\Edit_Booking_Room_Form;

class Booking_Service_Controller extends Controller {
	/**
	 * Handle create new service.
	 *
	 * @param  \Awethemes\Http\Request $request The current request.
	 * @return \Awethemes\Http\Response
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
	 * @param  \Awethemes\Http\Request $request The current request.
	 * @return \Awethemes\Http\Response
	 */
	public function store( Request $request ) {
		check_admin_referer( 'create_booking_service', '_wpnonce' );

		if ( ! $request->filled( '_refer' ) || ! $booking = abrs_get_booking( $request['_refer'] ) ) {
			return $this->whoops();
		}

		$list_services = $request['list_services'] ? $request['list_services'] : [];
		$services_item_exist = $booking->get_services();
		$ids = $services_item_exist->pluck( 'service_id' )->all();

		$ids_deleted = array_diff( $ids, $list_services );
		$ids_created = array_diff( $list_services, $ids );

		if ( ! empty( $ids_deleted ) ) {
			foreach ( $ids_deleted as $service_id ) {
				$item_id = $services_item_exist->where( 'service_id', '==', $service_id )->pluck( 'id' )->first();
				abrs_delete_booking_item( new Service_Item( $item_id ) );
			}
		}

		if ( ! empty( $ids_created ) ) {
			foreach ( $ids_created as $service_id ) {
				$service = new Service( $service_id );
				$service_item = new Service_Item;
				$service_item['booking_id']        = $booking->get_id();
				$service_item['service_id']        = absint( $service->get_id() );
				$service_item['service_operation'] = abrs_sanitize_html( $service->get( 'operation' ) );
				$service_item['service_value']     = abrs_sanitize_decimal( $service->get( 'value' ) );

				$service_item->save();
			}
		}

		return $this->redirect()->to( get_edit_post_link( $booking->get_id(), 'raw' ) );
	}

	/**
	 * Perform delete a service item.
	 *
	 * @param  \Awethemes\Http\Request                $request      The current request.
	 * @param  \AweBooking\Model\Booking\Service_Item $service_item The booking service item.
	 * @return \Awethemes\Http\Response
	 */
	public function destroy( Request $request, Service_Item $service_item ) {
		check_admin_referer( 'delete_service_' . $service_item->get_id(), '_wpnonce' );

		abrs_delete_booking_item( $service_item );

		abrs_admin_notices( esc_html__( 'The service has been destroyed!', 'awebooking' ), 'info' )->dialog();

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
