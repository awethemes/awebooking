<?php
namespace AweBooking\Reservation;

use AweBooking\AweBooking;
use \AweBooking\Model\Common\Guest_Counts;

class Url_Generator {


	/**
	 * Return the search link.
	 *
	 * @param  \\AweBooking\Model\Common\Guest_Counts $guest The guest.
	 * @return string
	 */
	public function get_search_link( Guest $guest ) {
		$base_url = $this->get_page_permalink( 'check_availability' );

		if ( $session_id = $this->reservation->get_session_id() ) {
			return add_query_arg( 'session_id', $session_id, $base_url );
		}

		$timespan = $this->reservation->get_timespan();

		$add_query = [
			'check_in'  => $timespan->to_array()[0],
			'check_out' => $timespan->to_array()[1],
			'adults'    => $guest->get_adults(),
		];

		if ( awebooking( 'setting' )->is_children_bookable() && $children = $guest->get_children() ) {
			$add_query['children'] = $children;
		}

		if ( awebooking( 'setting' )->is_infants_bookable() && $infants = $guest->get_infants() ) {
			$add_query['infants'] = $children;
		}

		return add_query_arg( $add_query, $base_url );
	}
}
