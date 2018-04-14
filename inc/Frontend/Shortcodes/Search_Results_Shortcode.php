<?php
namespace AweBooking\Frontend\Shortcodes;

class Search_Results_Shortcode extends Shortcode_Abstract {
	/**
	 * {@inheritdoc}
	 */
	public function output( $request ) {
		// Display the search form if direct visit to page.
		if ( ! $request->filled( 'check-in', 'check-out' ) ) {
			abrs_get_template( 'search/search-form.php' );
			return;
		}

		// Create new reservation request instance.
		$res_request = abrs_reservation_request([
			'check_in'   => sanitize_text_field( $request->get( 'check-in' ) ),
			'check_out'  => sanitize_text_field( $request->get( 'check-out' ) ),
			'adults'     => absint( $request->get( 'adults', 1 ) ),
			'children'   => abrs_children_bookable() ? absint( $request->get( 'children', 0 ) ) : -1,
			'infants'    => abrs_children_bookable() ? absint( $request->get( 'infants', 0 ) ) : -1,
		]);

		// Something went wrong.
		if ( ! $res_request || is_wp_error( $res_request ) ) {
			return; // TODO: ...
		}

		$constraints = [];
		$results = $res_request->search( $constraints );

		// Output the content.
		if ( $results->isEmpty() ) {
			abrs_get_template( 'search/no-results.php', compact( 'results' ) );
		} else {
			abrs_get_template( 'search/results.php', compact( 'results', 'res_request', 'request' ) );
		}
	}
}
