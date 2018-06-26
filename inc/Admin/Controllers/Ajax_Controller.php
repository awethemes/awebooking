<?php
namespace AweBooking\Admin\Controllers;

use Awethemes\Http\Request;
use Awethemes\Http\Json_Response;

class Ajax_Controller extends Controller {
	/**
	 * Handle add booking note.
	 *
	 * @param  \Awethemes\Http\Request $request The current request.
	 * @return mixed
	 */
	public function add_booking_note( Request $request ) {
		if ( ! check_ajax_referer( 'awebooking_add_note', null, false ) ) {
			return $this->json_response( 'error', esc_html__( 'Something went wrong.', 'awebooking' ) );
		}

		$booking       = absint( $request->booking );
		$note          = wp_kses_post( trim( stripslashes( $request->note ) ) );
		$customer_note = ( 'customer' === $request->note_type );

		if ( $booking <= 0 || empty( $note ) ) {
			return $this->json_response( 'error', esc_html__( 'Please enter some content to note.', 'awebooking' ) );
		}

		// Perform add booking note.
		$comment_id = abrs_add_booking_note( $booking, $note, $customer_note, true );

		if ( ! $comment_id || is_wp_error( $comment_id ) ) {
			return $this->json_response( 'error', esc_html__( 'Could not create note, please try again.', 'awebooking' ) );
		}

		// Response back HTML booking note.
		$note = abrs_get_booking_note( $comment_id );
		$data = abrs_admin_template( ABRS_ADMIN_PATH . '/Metaboxes/views/html-booking-note.php', compact( 'note' ) );

		return $this->json_response( 'success', null, $data );
	}

	/**
	 * Handle add booking note.
	 *
	 * @param  \Awethemes\Http\Request $request The current request.
	 * @param  int                     $note    The booking note ID to delete.
	 * @return mixed
	 */
	public function delete_booking_note( Request $request, $note ) {
		if ( ! check_ajax_referer( 'awebooking_delete_note', null, false ) ) {
			return $this->json_response( 'error', esc_html__( 'Something went wrong.', 'awebooking' ) );
		}

		$deleted = abrs_delete_booking_note( $note );

		return $this->json_response( $deleted ? 'success' : 'failure' );
	}

	/**
	 * Query for customers.
	 *
	 * @param  \Awethemes\Http\Request $request The current request.
	 * @return \Awethemes\Http\Response|mixed
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
				'last_name'  => $customer->last_name,
				/* translators: 1: user display name 2: user ID 3: user email */
				'display'    => sprintf( esc_html__( '%1$s (#%2$s - %3$s)', 'awebooking' ), $customer->first_name . ' ' . $customer->last_name, $customer->ID, $customer->user_email ),
			];
		}

		return new Json_Response( $found_customers );
	}

	/**
	 * Query for services.
	 *
	 * @param  \Awethemes\Http\Request $request The current request.
	 * @return \Awethemes\Http\Response|mixed
	 */
	public function search_services( Request $request ) {
		$term = abrs_clean( stripslashes( $request->get( 'term' ) ) );
		if ( empty( $term ) ) {
			return [];
		}

		$services = [];

		// First, check if term is numeric so we just search by ID.
		if ( is_numeric( $term ) ) {
			$service = abrs_get_service( absint( $term ) );

			if ( $service ) {
				$services = abrs_collect( [ $service ] );
			}
		}

		if ( empty( $services ) ) {
			$services = abrs_list_services([
				's'            => $term,
				'post__not_in' => wp_parse_id_list( $request->get( 'exclude', [] ) ),
			]);
		}

		return $services->map(function( $items ) {
			$items['label'] = '<div>
			<label class="label">' . $items['name'] . '</label>
			<div class="description">' . abrs_format_service_price( $items->get( 'value' ), $items->get( 'operation' ) ) . '</div></div>';

			return $items;
		});
	}

	/**
	 * Send a json_response to client.
	 *
	 * @param  string $status  The status code or string status (error or success).
	 * @param  string $message Optional, the messages.
	 * @param  array  $data    Optional, data send to browser.
	 * @param  array  $headers Optional, response headers.
	 * @return \Awethemes\Http\Json_Response
	 */
	protected function json_response( $status = 'success', $message = null, $data = null, $headers = [] ) {
		return new Json_Response( array_filter( compact( 'status', 'message', 'data' ) ), 'error' === $status ? 400 : 200, $headers );
	}
}
