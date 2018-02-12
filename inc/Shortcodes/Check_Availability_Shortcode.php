<?php
namespace AweBooking\Shortcodes;

use WP_Error;
use AweBooking\Reservation\Creator;
use AweBooking\Reservation\Reservation;
use AweBooking\Reservation\Searcher\Constraints\Session_Reservation_Constraint;
use AweBooking\Support\Utils as U;
use Awethemes\Http\Request;

class Check_Availability_Shortcode extends Shortcode {
	/**
	 * {@inheritdoc}
	 */
	public function output( Request $request ) {
		$creator = awebooking()->make( Creator::class );

		if ( $request->filled( 'session_id' ) ) {
			$reservation = $this->resolve_session_reservation( $request );
		} elseif ( $request->filled( 'check_in', 'check_out', 'adults' ) ) {
			$reservation = $creator->create_reservation_from_request( $request );

			if ( is_wp_error( $reservation ) ) {
				$this->get_session()->store( $reservation );
			}
		} else {
			$this->get_session()->flush();

			return $this->template( 'search/search-form.php' );
		}

		if ( is_null( $reservation ) || is_wp_error( $reservation ) ) {
			return $this->print_error( $reservation ?: esc_html__( 'Some error has occurred, please try again.', 'awebooking' ) );
		}

		$results = $this->perform_search_rooms( $reservation );

		return $this->template( 'search/results.php', compact( 'reservation', 'results' ) );
	}

	/**
	 * Get the reservation session.
	 *
	 * @return \AweBooking\Reservation\Session
	 */
	protected function get_session() {
		return awebooking()->make( 'reservation_session' );
	}

	/**
	 * Resolve the reservation from request or session.
	 *
	 * @param  \Awethemes\Http\Request $request The current request.
	 * @return \AweBooking\Reservation\Reservation|WP_Error|null
	 */
	protected function resolve_session_reservation( Request $request ) {
		$session = awebooking()->make( 'reservation_session' );

		$reservation = U::rescue( function() use ( $session, $request ) {
			return $session->resolve( $request->get( 'session_id' ) );
		});

		if ( is_null( $reservation ) ) {
			return new WP_Error( 'reservation_expired', esc_html__( 'The reservation session has been expired.', 'awebooking' ) );
		}

		return $reservation;
	}

	/**
	 * Perform search rooms.
	 *
	 * @param  \AweBooking\Reservation\Reservation $reservation The reservation instance.
	 * @return \\AweBooking\Reservation\Searcher\Results
	 */
	protected function perform_search_rooms( Reservation $reservation ) {
		$constraints = apply_filters( 'awebooking/reservation/constraints', [
			new Session_Reservation_Constraint( $reservation ),
		], $reservation );

		return $reservation->search( $constraints )
			->only_available_items();
	}
}
