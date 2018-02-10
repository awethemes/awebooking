<?php
namespace AweBooking\Shortcodes;

use WP_Error;
use Awethemes\Http\Request;
use AweBooking\Model\Stay;
use AweBooking\Model\Guest;
use AweBooking\Reservation\Reservation;
use AweBooking\Reservation\Searcher\Constraints\Session_Reservation_Constraint;
use AweBooking\Support\Utils as U;

class Check_Availability_Shortcode extends Shortcode {
	/**
	 * {@inheritdoc}
	 */
	public function output( Request $request ) {
		if ( $request->filled( 'check_in', 'check_out', 'adults' ) ) {
			$guest = $this->create_guest_from_request( $request );
			if ( is_null( $guest ) ) {
				return $this->print_error( esc_html__( 'The guest number is invalid.', 'awebooking' ) );
			}

			$reservation = $this->resolve_reservation( $request );
			if ( is_null( $reservation ) || is_wp_error( $reservation ) ) {
				return $this->print_error( $reservation ?: esc_html__( 'Some error has occurred, please try again.', 'awebooking' ) );
			}

			$results = $this->perform_search_rooms( $reservation, $guest );

			return $this->template( 'search/results.php',
				compact( 'reservation', 'guest', 'results' )
			);
		}

		return $this->template( 'search/welcome-screen.php' );
	}

	/**
	 * Create Guest from request.
	 *
	 * @param  \Awethemes\Http\Request $request The current request.
	 * @return \AweBooking\Model\Guest|null
	 */
	protected function create_guest_from_request( Request $request ) {
		return U::rescue( function() use ( $request ) {
			$guest = new Guest( $request->input( 'adults' ) );

			if ( awebooking( 'setting' )->is_children_bookable() && $request->filled( 'children' ) ) {
				$guest->set_children( $request->input( 'children' ) );
			}

			if ( awebooking( 'setting' )->is_infants_bookable() && $request->filled( 'infants' ) ) {
				$guest->set_infants( $request->input( 'infants' ) );
			}

			return $guest;
		});
	}

	/**
	 * Resolve the reservation from request or session.
	 *
	 * @param  \Awethemes\Http\Request $request The current request.
	 * @return \AweBooking\Reservation\Reservation|WP_Error|null
	 */
	protected function resolve_reservation( Request $request ) {
		$session = awebooking()->make( 'reservation_session' );

		if ( $request->filled( 'session_id' ) ) {
			$reservation = U::rescue( function() use ( $session, $request ) {
				return $session->resolve( $request->get( 'session_id' ) );
			});
		}

		$reservation = $this->create_reservation_from_request( $request );

		if ( ! is_wp_error( $reservation ) ) {
			// Store new reservation in the session.
			$session->store( $reservation );
		}

		return $reservation;
	}

	/**
	 * Create new reservation from request.
	 *
	 * @param  \Awethemes\Http\Request $request The current request.
	 * @return \AweBooking\Reservation\Reservation
	 */
	protected function create_reservation_from_request( Request $request ) {
		$source = awebooking( 'reservation_sources' )->get( 'direct_website' );

		if ( is_null( $source ) ) {
			return new WP_Error( 'source_error', esc_html__( 'Sorry, the source was not found!', 'awebooking' ) );
		}

		try {
			$stay = new Stay( $request->input( 'check_in' ), $request->input( 'check_out' ) );
			$stay->require_minimum_nights( 1 );
		} catch ( \Exception $e ) {
			return new WP_Error( 'stay_error', $e->getMessage() );
		}

		return new Reservation( $source, $stay );
	}

	/**
	 * Perform search rooms.
	 *
	 * @param  \AweBooking\Reservation\Reservation $reservation The reservation instance.
	 * @param  \AweBooking\Model\Guest             $guest       The guest instance.
	 * @return \\AweBooking\Reservation\Searcher\Results
	 */
	protected function perform_search_rooms( Reservation $reservation, Guest $guest ) {
		$constraints = apply_filters( 'awebooking/reservation/constraints', [
			new Session_Reservation_Constraint( $reservation ),
		], $reservation, $guest );

		return $reservation->search( $guest, $constraints )
			->only_available_items();
	}
}
