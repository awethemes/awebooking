<?php
namespace AweBooking\Frontend\Shortcodes;

use AweBooking\Reservation\Reservation;
use AweBooking\Availability\Constraints\Reservation_Constraint;

class Search_Results_Shortcode extends Shortcode_Abstract {
	/**
	 * The reservation instance.
	 *
	 * @var \AweBooking\Reservation\Reservation
	 */
	protected $reservation;

	/**
	 * Constructor.
	 *
	 * @param \AweBooking\Reservation\Reservation $reservation The reservation instance.
	 */
	public function __construct( Reservation $reservation ) {
		$this->reservation = $reservation;
	}

	/**
	 * {@inheritdoc}
	 */
	public function output( $request ) {
		global $wp;

		// No reservation request found, just show the search form.
		if ( empty( $wp->query_vars['res_request'] ) ) {
			return abrs_get_template( 'search/search-form.php' );
		}

		// Retrieve the reservation request, this can be
		// is a WP_Error so let's check it.
		$res_request = $wp->query_vars['res_request'];

		if ( is_wp_error( $res_request ) ) {
			$this->print_error( $res_request );
			return;
		}

		$contraints = apply_filters( 'awebooking/', [ // TODO: ...
			new Reservation_Constraint( $this->reservation ),
		]);

		// Query the rooms.
		$results = $res_request
			->add_contraints( $contraints )
			->search();

		if ( ! $results->has_items() ) {
			abrs_get_template( 'search/no-results.php', compact( 'results' ) );
		} else {
			abrs_get_template( 'search/results.php', compact( 'results', 'res_request' ) );
		}
	}
}
