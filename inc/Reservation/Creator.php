<?php
namespace AweBooking\Reservation;

use WP_Error;
use AweBooking\Setting;
use AweBooking\AweBooking;
use AweBooking\Model\Room;
use AweBooking\Model\Stay;
use AweBooking\Model\Guest;
use AweBooking\Model\Booking;
use AweBooking\Model\Booking_Room_Item;
use AweBooking\Support\Utils as U;
use Awethemes\Http\Request;

class Creator {
	/**
	 * The AweBooking instance.
	 *
	 * @var \AweBooking\AweBooking
	 */
	protected $awebooking;

	/**
	 * The setting instance.
	 *
	 * @var \AweBooking\Setting
	 */
	protected $setting;

	/**
	 * Create new gateway.
	 *
	 * @param \AweBooking\AweBooking $awebooking The awebooking instance.
	 * @param \AweBooking\Setting    $setting    The setting instance.
	 */
	public function __construct( AweBooking $awebooking, Setting $setting ) {
		$this->awebooking = $awebooking;
		$this->setting = $setting;
	}

	/**
	 * Create Guest from request.
	 *
	 * @param  \Awethemes\Http\Request $request The current request.
	 * @return \AweBooking\Model\Guest|null
	 */
	public function create_guest_from_request( Request $request ) {
		return U::rescue( function() use ( $request ) {
			$guest = new Guest( $request->input( 'adults' ) );

			if ( $this->setting->is_children_bookable() && $request->filled( 'children' ) ) {
				$guest->set_children( $request->input( 'children' ) );
			}

			if ( $this->setting->is_infants_bookable() && $request->filled( 'infants' ) ) {
				$guest->set_infants( $request->input( 'infants' ) );
			}

			return $guest;
		});
	}

	/**
	 * Create new reservation from request.
	 *
	 * @param  \Awethemes\Http\Request $request The current request.
	 * @return \AweBooking\Reservation\Reservation|WP_Error
	 */
	public function create_reservation_from_request( Request $request ) {
		$source = $this->awebooking->make( 'reservation_sources' )->get(
			apply_filters( 'awebooking/reservation/default_reservation_source', 'direct_website' )
		);

		if ( is_null( $source ) ) {
			return new WP_Error( 'source_error', esc_html__( 'The source was not found!', 'awebooking' ) );
		}

		try {
			$stay = new Stay( $request->input( 'check_in' ), $request->input( 'check_out' ) );
			$stay->require_minimum_nights( 1 );
		} catch ( \Exception $e ) {
			return new WP_Error( 'stay_error', $e->getMessage() );
		}

		$guest = $this->create_guest_from_request( $request );
		if ( is_null( $guest ) ) {
			return new WP_Error( 'source_error', esc_html__( 'Invalid the guest data!', 'awebooking' ) );
		}

		return new Reservation( $source, $stay, $guest );
	}

	/**
	 * Create new booking room item from reservation item.
	 *
	 * @param  \AweBooking\Reservation\Item $item    The reservation item.
	 * @param  \AweBooking\Model\Booking    $booking Optional, the Booking this item belongs to.
	 * @return \AweBooking\Model\Booking_Room_Item
	 */
	public function create_booking_room( Item $item, Booking $booking = null ) {
		$room_item = new Booking_Room_Item;

		$room_item->fill([
			'name'         => $item->get_label(),
			'booking_id'   => $booking ? $booking->get_id() : null,
			'room_id'      => $item->room->get_id(),
			'check_in'     => $item->stay->get_check_in()->toDateString(),
			'check_out'    => $item->stay->get_check_out()->toDateString(),

			'adults'       => $item->guest->get_adults(),
			'children'     => $this->setting->is_children_bookable() ? $item->guest->get_children() : null,
			'infants'      => $this->setting->is_infants_bookable() ? $item->guest->get_infants() : null,

			'subtotal'     => 0, // Pre-discount.
			'total'        => 0,
		]);

		return $room_item;
	}
}
