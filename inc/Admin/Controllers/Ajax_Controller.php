<?php
namespace AweBooking\Admin\Controllers;

use Awethemes\Http\Request;

class Ajax_Controller extends Controller {
	/**
	 * Search for customers.
	 *
	 * @param  \Awethemes\Http\Request $request The current request.
	 * @return \Awethemes\Http\Response
	 */
	public function search_customers( Request $request ) {
		$term = abrs_clean( stripslashes( $request->get( 'term' ) ) );
		if ( empty( $term ) ) {
			return [];
		}

		// Begin build the customers IDs.
		$ids = [];

		// First, check if term is numeric so we just search by ID.
		if ( is_numeric( $term ) ) {
			$customer = get_userdata( absint( $term ) );

			if ( $customer && 0 !== $customer->ID ) {
				$ids = [ $customer->ID ];
			}
		}

		// Exclude IDs if requested.
		if ( $request->filled( 'exclude' ) ) {
			$ids = array_diff( $ids, wp_parse_id_list( $request->get( 'exclude' ) ) );
		}

		// Usernames can be numeric so we first check that no users was found by ID before
		// searching for numeric username, this prevents performance issues with ID lookups.
		if ( empty( $ids ) ) {
			// If search is smaller than 3 characters, limit result set to avoid
			// too many rows being returned.
			$limit = ( strlen( $term ) < 3 ) ? 20 : 0;

			$ids = abrs_search_customers( $term, $limit );
		}

		// Now, let's build the results.
		$found_customers = [];

		foreach ( $ids as $id ) {
			$customer = get_userdata( $id );
			if ( ! $customer ) {
				continue;
			}

			$found_customers[] = [
				'id'         => $id,
				'email'      => $customer->user_email,
				'first_name' => $customer->first_name,
				'first_name' => $customer->last_name,
				/* translators: 1: user display name 2: user ID 3: user email */
				'display'    => sprintf( esc_html__( '%1$s (#%2$s - %3$s)', 'awebooking' ), $customer->first_name . ' ' . $customer->last_name, $customer->ID, $customer->user_email ),
			];
		}

		return $found_customers;
	}
}
