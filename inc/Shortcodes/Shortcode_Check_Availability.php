<?php
namespace AweBooking\Shortcodes;

use WP_Error;
use AweBooking\Factory;
use AweBooking\AweBooking;
use AweBooking\Template;

use AweBooking\Reservation\Searcher\Query;
use AweBooking\Reservation\Item as Reservation_Item;
use AweBooking\Reservation\Reservation;
use AweBooking\Admin\Forms\New_Reservation_Form;
use AweBooking\Admin\List_Tables\Availability_List_Table;

use Illuminate\Support\Arr;
use Awethemes\Http\Request;

use AweBooking\Model\Stay;
use AweBooking\Model\Room;
use AweBooking\Model\Rate;
use AweBooking\Model\Guest;
use AweBooking\Model\Room_Type;

use AweBooking\Model\Exceptions\Model_Not_Found_Exception;
use AweBooking\Http\Exceptions\Validation_Failed_Exception;
use AweBooking\Http\Exceptions\Nonce_Mismatch_Exception;
use AweBooking\Reservation\Searcher\Constraints\Session_Reservation_Constraint;

class Shortcode_Check_Availability {

	/**
	 * Get the shortcode content.
	 *
	 * @param array $atts
	 * @return string
	 */
	public static function get( $atts ) {
		return Shortcodes::shortcode_wrapper( array( __CLASS__, 'output' ), $atts );
	}

	/**
	 * Output the shortcode.
	 *
	 * @param array $atts
	 */
	public static function output( $atts ) {

		$atts = shortcode_atts( array(), $atts, 'awebooking_check_availability' );

		self::check_availability();
	}

	/**
	 * Show the checkout.
	 */
	private static function check_availability() {
		$errors = '';
		$results = [];

		$request = awebooking()->make( 'request' );

		self::step_search( $request );
	}

	protected static function step_search( Request $request ) {
		$session = awebooking()->make( 'reservation_session' );
		$resolve = $session->resolve();

		// Create new reservation from request.
		$reservation = self::new_reservation_from_request( $request );

		if ( ! $resolve || ( ! self::is_new_stay( $reservation, $session->resolve() ) ) ) {

			// Store in the session.
			$session->store( $reservation );
		} else {
			// Try to resolve from session.
			$reservation = $session->resolve();
		}

		$guest = new Guest( $request->get( 'adults' ), $request->get( 'children' ), $request->get( 'infants' ) );

		// Attach the search to availability_table items.
		$items = self::perform_search_rooms( $reservation, $guest );

		Template::get_template( 'check-availability.php', compact(
			'reservation', 'items', 'guest'
		) );
	}

	/**
	 * Perform search rooms.
	 *
	 * @param  \AweBooking\Reservation\Reservation $reservation The reservation instance.
	 * @return array
	 */
	protected static function perform_search_rooms( Reservation $reservation, Guest $guest ) {
		$constraints = [
			new Session_Reservation_Constraint( $reservation ),
		];

		$results = $reservation->search( $guest, $constraints )
			->only_available_items();

		return $results;
	}

	/**
	 * Create new reservation from request.
	 *
	 * @param  \Awethemes\Http\Request $r The current request.
	 * @return \AweBooking\Reservation\Reservation
	 *
	 * @throws \InvalidArgumentException
	 */
	protected static function new_reservation_from_request( Request $request ) {
		$source = awebooking( 'reservation_sources' )->get( 'direct_website' );

		return new Reservation( $source,
			new Stay( $request->get( 'start-date' ), $request->get( 'end-date' ) )
		);
	}

	protected static function is_new_stay( Reservation $reservation, Reservation $other_reservation ) {

		if ( $reservation->get_stay()->get_check_in() != $other_reservation->get_stay()->get_check_in() ) {
			return false;
		}

		if ( $reservation->get_stay()->get_check_out() != $other_reservation->get_stay()->get_check_out() ) {
			return false;
		}

		return true;
	}
}
