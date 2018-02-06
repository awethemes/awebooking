<?php
namespace AweBooking\Admin\Controllers;

use Awethemes\Http\Request;

class Ajax_Controller extends Controller {
	/**
	 * Search for customers and return json.
	 */
	public function search_customers( Request $request ) {
		$request->validate( [ 'term' => 'required' ] );

		$term    = sanitize_text_field( wp_unslash( $_GET['term'] ) );
		$exclude = array();
		$limit   = '';

		if ( empty( $term ) ) {
			wp_die();
		}

		// Search by ID.
		if ( is_numeric( $term ) ) {
			$customer = get_userdata( intval( $term ) );

			// Customer does not exists.
			if ( $customer instanceof \WP_User ) {
				wp_die();
			}

			$ids = array( $customer->ID );
		} else {
			// If search is smaller than 3 characters, limit result set to avoid
			// too many rows being returned.
			if ( 3 > strlen( $term ) ) {
				$limit = 20;
			}

			$ids = $this->search_customers( $term, $limit );
		}

		$found_customers = array();

		if ( ! empty( $_GET['exclude'] ) ) {
			$ids = array_diff( $ids, (array) $_GET['exclude'] );
		}

		foreach ( $ids as $id ) {
			$customer = get_userdata( $id );

			/* translators: 1: user display name 2: user ID 3: user email */
			$found_customers[ $id ] = sprintf(
				esc_html__( '%1$s (#%2$s &ndash; %3$s)', 'awebooking' ),
				$customer->first_name . ' ' . $customer->last_name,
				$customer->ID,
				$customer->user_email
			);
		}

		wp_send_json( apply_filters( 'awebooking_json_search_found_customers', $found_customers ) );
	}
}
