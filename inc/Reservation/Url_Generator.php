<?php
namespace AweBooking\Reservation;

use AweBooking\AweBooking;
use AweBooking\Model\Guest;
use AweBooking\Http\Routing\Url_Generator as Base_Url_Generator;

class Url_Generator extends Base_Url_Generator {
	/**
	 * The reservation instance.
	 *
	 * @var \AweBooking\Reservation\Reservation
	 */
	protected $reservation;

	/**
	 * Constructor.
	 *
	 * @param \AweBooking\AweBooking              $awebooking  The AweBooking.
	 * @param \AweBooking\Reservation\Reservation $reservation The reservation.
	 */
	public function __construct( AweBooking $awebooking, Reservation $reservation ) {
		$this->reservation = $reservation;

		parent::__construct( $awebooking );
	}

	/**
	 * Return the search link.
	 *
	 * @param  \AweBooking\Model\Guest $guest The guest.
	 * @return string
	 */
	public function get_search_link( Guest $guest, $with_session_id = false ) {
		$stay = $this->reservation->get_stay();

		$base_url = $this->get_page_permalink( 'check_availability' );

		$add_query = [
			'check_in'  => $stay->to_array()[0],
			'check_out' => $stay->to_array()[1],
			'adults'    => $guest->get_adults(),
		];

		if ( awebooking( 'setting' )->is_children_bookable() && $children = $guest->get_children() ) {
			$add_query['children'] = $children;
		}

		if ( awebooking( 'setting' )->is_infants_bookable() && $infants = $guest->get_infants() ) {
			$add_query['infants'] = $children;
		}

		if ( $with_session_id ) {
			$add_query['session_id'] = $this->reservation->generate_session_id();
		}

		return add_query_arg( $add_query, $base_url );
	}
}