<?php
namespace AweBooking\Admin\Controllers;

use WP_Error;
use AweBooking\Assert;
use AweBooking\Factory;
use AweBooking\Model\Stay;
use AweBooking\Model\Guest;
use AweBooking\Model\Rate;
use AweBooking\Model\Booking;
use AweBooking\Model\Booking_Room_Item;
use AweBooking\Reservation\Creator;
use AweBooking\Reservation\Reservation;
use AweBooking\Reservation\Searcher\Checker;
use AweBooking\Reservation\Searcher\Constraints\Rooms_In_Booking_Constraint;
use AweBooking\Admin\Forms\Search_Reservation_Form;
use AweBooking\Admin\List_Tables\Availability_List_Table;
use AweBooking\Admin\Forms\Edit_Room_Item_Form;
use AweBooking\Support\Utils as U;
use Awethemes\Http\Request;
use Illuminate\Support\Arr;

class Booking_Room_Controller extends Controller {
	/**
	 * Handle store the settings.
	 *
	 * @param  \Awethemes\Http\Request   $request The current request.
	 * @param  \AweBooking\Model\Booking $booking The booking reference.
	 * @return \Awethemes\Http\Response
	 */
	public function create( Request $request, Booking $booking ) {
		if ( ! $booking->is_editable() ) {
			return new WP_Error( 'error', esc_html__( 'This booking is no longer editable.', 'awebooking' ) );
		}

		$controls = new Search_Reservation_Form( 'minimal' );
		$controls->set_request( $request );

		if ( $request->filled( 'check_in_out' ) ) {
			$reservation = U::rescue( function() use ( $request, $booking ) {
				return $this->create_booking_reservation( $request, $booking );
			});

			if ( ! is_null( $reservation ) ) {
				$availability_table = new Availability_List_Table( $reservation );
				$availability_table->items = $this->perform_search_items( $reservation, $booking );
			}
		}

		return $this->response_view( 'booking/add-room.php', compact(
			'request', 'booking', 'controls', 'availability_table'
		));
	}

	/**
	 * Perform search items.
	 *
	 * @param  \AweBooking\Reservation\Reservation $reservation The reservation instance.
	 *  @param \AweBooking\Model\Booking           $booking     The booking reference.
	 * @return \AweBooking\Reservation\Searcher\Results
	 */
	protected function perform_search_items( Reservation $reservation, Booking $booking ) {
		$constraints = apply_filters( 'awebooking/add_room_reservation/constraints', [
			new Rooms_In_Booking_Constraint( $booking ),
		]);

		$results = $reservation->search( null, $constraints )
			->only_available_items();

		return apply_filters( 'awebooking/add_room_reservation/search_results', $results, $reservation, $booking );
	}

	/**
	 * Create new booking reservation.
	 *
	 * @param  \Awethemes\Http\Request   $request The current request.
	 * @param  \AweBooking\Model\Booking $booking The booking reference.
	 * @return \AweBooking\Reservation\Reservation|null
	 */
	protected function create_booking_reservation( Request $request, Booking $booking ) {
		// $soruce = $booking->get_source();
		$source = awebooking( 'reservation_sources' )->get( 'direct_website' );

		if ( $request->filled( 'check_in_out' ) ) {
			$check_in_out = (array) $request->input( 'check_in_out' );
		} else {
			$check_in_out = [ $request->input( 'check_in' ), $request->input( 'check_out' ) ];
		}

		return new Reservation( $source, new Stay( ...$check_in_out ) );
	}

	/**
	 * Handle store.
	 *
	 * @param  \Awethemes\Http\Request   $request The current request.
	 * @param  \AweBooking\Model\Booking $booking The booking reference.
	 * @return \Awethemes\Http\Response
	 */
	public function store( Request $request, Booking $booking ) {
		$request->verify_nonce( '_wpnonce', 'add_booking_room' );

		// Parse the submit data.
		$item_data = $this->parse_submit_data( $request );
		list( $room_type, $room_unit ) = $this->resolve_room_unit( $item_data );

		// Create the reservation from request.
		$reservation = $this->create_booking_reservation( $request, $booking );

		// Demo:
		$rate = new Rate( $room_type->get_id(), 'room_type' );

		try {
			$item = $reservation->add_room( $room_unit, $rate,
				new Guest( $item_data['adults'], $item_data['children'] )
			);
		} catch ( \Exception $e ) {
			$this->notices( 'error', $e->getMessage() );

			return $this->redirect()->back()->with_input();
		}

		$room_item = ( new Creator )->create_booking_room( $item, $booking );

		if ( $room_item->save() ) {
			$this->notices( 'success', esc_html__( 'Added room item successfully!', 'awebooking' ) );
		} else {
			$this->notices( 'warning', esc_html__( 'Error when add room', 'awebooking' ) );
		}

		return $this->redirect()->to( $booking->get_edit_url() );
	}

	/**
	 * Parse the submit_data.
	 *
	 * @param  \Awethemes\Http\Request $request The current request.
	 * @return array
	 */
	protected function parse_submit_data( Request $request ) {
		// Get the submited room-item.
		$submited_item = Arr::first( array_keys( (array) $request['submit'] ) );

		// Build the add room-item data.
		$item_data = (array) $request->input( "reservation_room.{$submited_item}" );

		return array_merge( $item_data, [
			'room_type' => $submited_item,
			'check_in'  => $request->check_in,
			'check_out' => $request->check_out,
		]);
	}

	/**
	 * Resolve the room_unit from trusted data.
	 *
	 * @param  array $item_data The trusted data.
	 * @return array
	 */
	protected function resolve_room_unit( array $item_data ) {
		// Validate the room_type.
		$room_type = Factory::get_room_type( $item_data['room_type'] );
		Assert::object_exists( $room_type );

		$room_unit = $room_type->get_room( $item_data['room_unit'] );
		Assert::object_exists( $room_unit );

		return [ $room_type, $room_unit ];
	}

	/**
	 * Show edit for.
	 *
	 * @param  \Awethemes\Http\Request             $request      The current request.
	 * @param  \AweBooking\Model\Booking           $booking      The booking reference.
	 * @param  \AweBooking\Model\Booking_Room_Item $room_item The payment item.
	 * @return \Awethemes\Http\Response
	 */
	public function edit( Request $request, Booking $booking, Booking_Room_Item $room_item ) {
		Assert::booking_item( $room_item, $booking );

		$controls = new Edit_Room_Item_Form( $room_item );

		$controls->fill( $room_item->get_attributes() );
		$controls['check_in_out']->set_value( U::optional( $room_item->get_stay() )->to_array() );

		return $this->response_view( 'booking/edit-room.php', compact( 'booking', 'controls', 'room_item' ) );
	}

	/**
	 * Perform update a payment item.
	 *
	 * @param  \Awethemes\Http\Request             $request   The current request.
	 * @param  \AweBooking\Model\Booking           $booking   The booking reference.
	 * @param  \AweBooking\Model\Booking_Room_Item $room_item The payment item.
	 * @return \Awethemes\Http\Response
	 */
	public function update( Request $request, Booking $booking, Booking_Room_Item $room_item ) {
		Assert::booking_item( $room_item, $booking );

		// First, verify the nonce.
		$request->verify_nonce( '_wpnonce', 'update_booking_room_' . $room_item->get_id() );

		// Next, verify the form controls.
		try {
			( new Edit_Room_Item_Form( $room_item ) )->handle( $request->all() );
		} catch ( \Exception $e ) {
			$this->notices( 'error', $e->getMessage() );

			return $this->redirect()->back( $room_item->get_edit_link() )
						->with_input();
		}

		// If "check_in_out" filled, let perform modify the stay date.
		if ( $request->filled( 'check_in_out' ) && is_array( $request['check_in_out'] ) ) {
			$this->perform_modify_stay( $request, $room_item );
		}

		// Fill the new data.
		$room_item->fill( $request->only( 'adults', 'children', 'infants', 'total' ) );
		$room_item->save();

		// Re-calculate the totals.
		$booking->calculate_totals();

		// Then, handle the redirect.
		if ( $this->notices()->has( 'error' ) ) {
			return $this->redirect()->back( $room_item->get_edit_link() );
		}

		$this->notices( 'info', esc_html__( 'Booking room has been successfully updated!', 'awebooking' ) );

		return $this->redirect()->to( $booking->get_edit_url() );
	}

	/**
	 * Perform modify the stay from request.
	 *
	 * @param  \Awethemes\Http\Request             $request   The current request.
	 * @param  \AweBooking\Model\Booking_Room_Item $room_item The payment item.
	 * @return WP_Error|null
	 */
	protected function perform_modify_stay( Request $request, Booking_Room_Item $room_item ) {
		if ( ! $request->filled( 'check_in_out' ) ) {
			return;
		}

		$stay = U::rescue( function () use ( $request ) {
			return new Stay( $request['check_in_out'][0], $request['check_in_out'][1] );
		});

		// Don't do anything if got invalid stay.
		if ( is_null( $stay ) ) {
			return;
		}

		$modified = $room_item->modify_stay( $stay );
		if ( is_wp_error( $modified ) ) {
			$this->notices( 'error', $modified->get_error_message() );
		}
	}

	/**
	 * Perform delete a booking item.
	 *
	 * @param  \Awethemes\Http\Request             $request The current request.
	 * @param  \AweBooking\Model\Booking           $booking The booking reference.
	 * @param  \AweBooking\Model\Booking_Room_Item $room_item The payment item.
	 * @return \Awethemes\Http\Response
	 */
	public function destroy( Request $request, Booking $booking, Booking_Room_Item $room_item ) {
		Assert::booking_item( $room_item, $booking );

		$request->verify_nonce( '_wpnonce', 'delete_line_item_' . $room_item->get_id() );

		$room_item->delete();
		$booking->calculate_totals();

		$this->notices( 'info', esc_html__( 'The room item has been deleted', 'awebooking' ) );

		return $this->redirect()->back( $booking->get_edit_url() );
	}
}
