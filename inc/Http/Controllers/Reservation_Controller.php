<?php
namespace AweBooking\Http\Controllers;

use AweBooking\Model\Factory;
use AweBooking\AweBooking;
use AweBooking\Model\Rate;
use \AweBooking\Model\Common\Guest_Counts;
use AweBooking\Reservation\Creator;
use AweBooking\Reservation\Reservation;
use AweBooking\Reservation\Url_Generator;
use Awethemes\Http\Request;
use Illuminate\Support\Arr;
use AweBooking\Reservation\Searcher\Checker;
use AweBooking\Reservation\Searcher\Constraints\Session_Reservation_Constraint;

class Reservation_Controller extends Controller {
	/**
	 * The awebooking instance.
	 *
	 * @var \AweBooking\AweBooking
	 */
	protected $awebooking;

	/**
	 * The reservation creator.
	 *
	 * @var \AweBooking\Reservation\Creator
	 */
	protected $creator;

	/**
	 * Constructor.
	 *
	 * @param \AweBooking\AweBooking          $awebooking The awebooking instance.
	 * @param \AweBooking\Reservation\Creator $creator    The creator instance.
	 */
	public function __construct( AweBooking $awebooking, Creator $creator ) {
		$this->awebooking = $awebooking;
		$this->creator = $creator;
	}

	/**
	 * Add a room-item in session reservation.
	 *
	 * @param  \Awethemes\Http\Request $request The current request.
	 * @return \Awethemes\Http\Response
	 *
	 * @throws \RuntimeException
	 */
	public function add_item( Request $request ) {
		$request->verify_nonce( '_wpnonce', 'awebooking_reservation' );

		$session = $this->awebooking->make( 'reservation_session' );

		if ( $request->filled( 'session_id' ) ) {
			$reservation = $session->resolve( $request->input( 'session_id' ) );

			if ( is_null( $reservation ) ) {
				$this->notices( 'error', esc_html__( 'The reservation session could not found, please try again.', 'awebooking' ) );
				return $this->redirect()->back();
			}
		} else {
			$reservation = $this->creator->create_reservation_from_request( $request );

			if ( is_wp_error( $reservation ) ) {
				$this->notices( 'error', $reservation->get_error_message() );
				return $this->redirect()->back();
			}

			$session->store( $reservation );
		}

		// Get the submited room-type.
		$requested_room_type = Arr::first( array_keys( (array) $request->submit ) );

		$this->perform_add_item( $reservation, $requested_room_type );
		$session->update( $reservation );

		$url_generator = new Url_Generator( awebooking()->get_instance(), $reservation );

		return $this->redirect()->to(
			$url_generator->get_search_link( new Guest( 1 ), true )
		);
	}

	protected function perform_add_item( $reservation, $room_type ) {
		$room_type = Factory::get_room_type( $room_type );

		$constraints = apply_filters( 'awebooking/reservation/constraints', [
			new Session_Reservation_Constraint( $reservation ),
		], $reservation );

		$availability = ( new Checker )->check( $room_type, $reservation->get_timespan(), $constraints );
		$remain_rooms = $availability->remain_rooms();

		if ( $remain_rooms->count() ) {
			$select_room = $remain_rooms->first();
			$select_room = apply_filters( 'awebooking/reservation/select_room', $select_room['room'], $availability, $reservation );

			$rate = new \AweBooking\Model\Rate;
			$reservation->add_room( $select_room, $rate, $reservation->get_guest() );
		}
	}
}
