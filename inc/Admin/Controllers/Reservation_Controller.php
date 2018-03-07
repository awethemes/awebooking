<?php
namespace AweBooking\Admin\Controllers;

use WP_Error;
use AweBooking\Assert;
use AweBooking\Factory;
use AweBooking\AweBooking;

use AweBooking\Reservation\Searcher\Query;
use AweBooking\Reservation\Item as Reservation_Item;
use AweBooking\Reservation\Reservation;
use AweBooking\Admin\Forms\Search_Reservation_Form;
use AweBooking\Admin\List_Tables\Availability_List_Table;

use Illuminate\Support\Arr;
use Awethemes\Http\Request;

use AweBooking\Model\Stay;
use AweBooking\Model\Room;
use AweBooking\Model\Rate;
use AweBooking\Model\Guest;
use AweBooking\Model\Room_Type;

use AweBooking\Http\Exceptions\Validation_Failed_Exception;
use AweBooking\Http\Exceptions\Nonce_Mismatch_Exception;
use AweBooking\Reservation\Searcher\Constraints\Session_Reservation_Constraint;

class Reservation_Controller extends Controller {
	/**
	 * The AweBooking instance.
	 *
	 * @var \AweBooking\AweBooking
	 */
	protected $awebooking;

	/**
	 * Constructor.
	 *
	 * @param \AweBooking\AweBooking $awebooking The AweBooking instance.
	 */
	public function __construct( AweBooking $awebooking ) {
		$this->awebooking = $awebooking;

		$this->check_capability( 'manage_awebooking' );
	}

	/**
	 * Create the new reservation.
	 *
	 * @param  \Awethemes\Http\Request $request The current request.
	 * @return \Awethemes\Http\Response
	 */
	public function create( Request $request ) {
		switch ( $request->get( 'step' ) ) {
			case 'search':
				return $this->step_search( $request );
		}

		return $this->response_view( 'reservation/create.php' );
	}

	protected function step_search( Request $request ) {
		$session = $this->awebooking->make( 'reservation_admin_session' );

		if ( $request->has( 'reservation_source' ) && $request->has( 'check_in_out' ) ) {
			try {
				( new Search_Reservation_Form )->handle( $request->all(), false );
			} catch ( Validation_Failed_Exception $e ) {
				return $this->redirect()->admin_route( 'reservation/create' );
			}

			// Create new reservation from request.
			$reservation = $this->new_reservation_from_request( $request );

			// Store in the session.
			$session->store( $reservation );
		} else {
			// Try to resolve from session.
			$reservation = $session->resolve();

			if ( is_null( $reservation ) ) {
				awebooking( 'admin_notices' )->warning( esc_html__( 'The reservation session has been expired', 'awebooking' ) );

				return $this->redirect()->admin_route( 'reservation/create' );
			}
		}

		$stay = new Stay( $request['check_in_out'][0], $request['check_in_out'][1] );

		// Attach the search to availability_table items.
		$availability_table = new Availability_List_Table( $reservation );

		$availability_table->items = $this->perform_search_rooms( $reservation, $stay );

		return $this->response_view( 'reservation/step-search.php', compact(
			'reservation', 'availability_table'
		) );
	}

	/**
	 * Perform search rooms.
	 *
	 * @param  \AweBooking\Reservation\Reservation $reservation The reservation instance.
	 * @return \\AweBooking\Reservation\Searcher\Results
	 */
	protected function perform_search_rooms( Reservation $reservation, $stay ) {
		$constraints = apply_filters( 'awebooking/admin/reservation/constraints', [] );

		return $reservation->search( $stay, null, $constraints )
			->only_available_items();
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
		$request->verify_nonce( '_wpnonce', 'awebooking_add_room' );

		// Try to resolve from session.
		$reservation = $this->awebooking['reservation_admin_session']->resolve();

		if ( is_null( $reservation ) ) {
			throw new \RuntimeException( esc_html__( 'The reservation session could not found, please try again.', 'awebooking' ) );
		}

		// Get the submited room-item.
		$submited_item = Arr::first( array_keys( (array) $request->submit ) );

		// Build the add room-item data.
		$item_data = (array) $request->input( "reservation_room.{$submited_item}" );
		$item_data['room_type'] = $submited_item;

		try {
			$this->add_reservation_item( $reservation, $item_data );
			awebooking( 'admin_notices' )->success( esc_html__( 'Item added successfully', 'awebooking' ) );
		} catch ( \Exception $e ) {
			awebooking( 'admin_notices' )->error( $e->getMessage() );
		}

		return $this->redirect()->admin_route( 'reservation/create', [ 'step' => 'search' ] );
	}

	/**
	 * Create new reservation from request.
	 *
	 * @param  \Awethemes\Http\Request $r The current request.
	 * @return \AweBooking\Reservation\Reservation
	 *
	 * @throws \InvalidArgumentException
	 */
	protected function new_reservation_from_request( Request $r ) {
		$source = $this->awebooking['reservation_sources']->get( $r['reservation_source'] );

		if ( is_null( $source ) ) {
			throw new \InvalidArgumentException( esc_html__( 'Sorry, the source was not found!', 'awebooking' ) );
		}

		return new Reservation( $source );
	}

	/**
	 * Add reservation item by given a trusted data.
	 *
	 * @param  Reservation $reservation The reservation instance.
	 * @param  array       $data        The reservation item data.
	 * @return void
	 */
	protected function add_reservation_item( Reservation $reservation, array $data ) {
		$data = wp_parse_args( $data, [
			'room_type'  => 0,
			'room_unit'  => 0,
			'room_rate'  => 0,
			'adults'     => 1,
			'children'   => 0,
			'infants'    => 0,
		]);

		// Validate the room_type.
		$room_type = Factory::get_room_type( $data['room_type'] );
		Assert::object_exists( $room_type );

		$room_unit = $room_type->get_room( $data['room_unit'] );
		Assert::object_exists( $room_unit );

		// ...
		$rate = new Rate( $room_type->get_id(), 'room_type' );

		// Add room into the reservation.
		$reservation->add_room( $room_unit, $rate,
			new Guest( $data['adults'], $data['children'], $data['infants'] )
		);

		// Update the reservation in the session store.
		$this->awebooking['reservation_admin_session']->update( $reservation );
	}
}
