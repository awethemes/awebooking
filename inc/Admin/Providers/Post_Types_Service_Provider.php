<?php

namespace AweBooking\Admin\Providers;

use AweBooking\Constants;
use AweBooking\Support\Service_Provider;
use AweBooking\Admin\List_Tables\Booking_List_Table;
use AweBooking\Admin\List_Tables\Room_Type_List_Table;

class Post_Types_Service_Provider extends Service_Provider {
	/**
	 * Init the hooks.
	 *
	 * @access private
	 */
	public function init() {
		// Load correct list table classes for current screen.
		add_action( 'current_screen', [ $this, 'setup_screen' ] );

		// Admin notices.
		add_filter( 'post_updated_messages', [ $this, 'post_updated_messages' ] );
		add_filter( 'bulk_post_updated_messages', [ $this, 'bulk_post_updated_messages' ], 10, 2 );

		// Disable Auto Save.
		add_action( 'admin_print_scripts', [ $this, 'disable_autosave' ] );

		// Extra post data and screen elements.
		add_filter( 'enter_title_here', [ $this, 'enter_title_here' ], 1, 2 );

		// Add a post display state for specified awebooking pages.
		add_filter( 'display_post_states', [ $this, 'display_post_states' ], 10, 2 );
	}

	/**
	 * Looks at the current screen and loads the correct list table handler.
	 *
	 * @param  \WP_Screen $screen The current screen.
	 * @access private
	 */
	public function setup_screen( $screen ) {
		switch ( $screen->id ) {
			case 'edit-room_type':
				$this->plugin->make( Room_Type_List_Table::class );
				break;

			case 'edit-awebooking':
				$this->plugin->make( Booking_List_Table::class );
				break;
		}
	}

	/**
	 * Change messages when a post type is updated.
	 *
	 * @param  array $messages Array of messages.
	 * @return array
	 */
	public function post_updated_messages( $messages ) {
		global $post;

		$messages[ Constants::ROOM_TYPE ] = [
			0 => '', // Unused. Messages start at index 1.
			/* translators: %s: The Room type url */
			1 => sprintf( __( 'Room type updated. <a href="%s">View</a>', 'awebooking' ), esc_url( get_permalink( $post->ID ) ) ),
			2 => esc_html__( 'Custom field updated.', 'awebooking' ),
			3 => esc_html__( 'Custom field deleted.', 'awebooking' ),
			4 => esc_html__( 'Room type updated.', 'awebooking' ),
			5 => esc_html__( 'Revision restored.', 'awebooking' ),
			/* translators: %s: The room type url */
			6 => sprintf( __( 'Room type published. <a href="%s">View</a>', 'awebooking' ), esc_url( get_permalink( $post->ID ) ) ),
			7 => esc_html__( 'Room type saved.', 'awebooking' ),
			/* translators: %s: The room type url */
			8 => sprintf( __( 'Room type submitted. <a target="_blank" href="%s">Preview</a>', 'awebooking' ), esc_url( add_query_arg( 'preview', 'true', get_permalink( $post->ID ) ) ) ),
			/* translators: 1: Date 2: The room type url */
			9 => sprintf( __( 'Room type scheduled for: %1$s. <a target="_blank" href="%2$s">Preview</a>', 'awebooking' ), '<strong>' . date_i18n( esc_html__( 'M j, Y @ G:i', 'awebooking' ), strtotime( $post->post_date ) ), esc_url( get_permalink( $post->ID ) ) . '</strong>' ),
			/* translators: %s: The room type url */
			10 => sprintf( __( 'Room type draft updated. <a target="_blank" href="%s">Preview</a>', 'awebooking' ), esc_url( add_query_arg( 'preview', 'true', get_permalink( $post->ID ) ) ) ),
		];

		$messages[ Constants::BOOKING ] = [
			0 => '', // Unused. Messages start at index 1.
			1 => esc_html__( 'Booking updated.', 'awebooking' ),
			2 => esc_html__( 'Custom field updated.', 'awebooking' ),
			3 => esc_html__( 'Custom field deleted.', 'awebooking' ),
			4 => esc_html__( 'Booking updated.', 'awebooking' ),
			5 => esc_html__( 'Revision restored.', 'awebooking' ),
			6 => esc_html__( 'Booking updated.', 'awebooking' ),
			7 => esc_html__( 'Booking saved.', 'awebooking' ),
			8 => esc_html__( 'Booking submitted.', 'awebooking' ),
			/* translators: %s: date */
			9 => sprintf( __( 'Booking scheduled for: %s.', 'awebooking' ), '<strong>' . date_i18n( esc_html__( 'M j, Y @ G:i', 'awebooking' ), strtotime( $post->post_date ) ) . '</strong>' ),
			10 => esc_html__( 'Booking draft updated.', 'awebooking' ),
			11 => esc_html__( 'Booking updated and sent.', 'awebooking' ),
		];

		return $messages;
	}

	/**
	 * Specify custom bulk actions messages for different post types.
	 *
	 * @param  array $bulk_messages Array of messages.
	 * @param  array $bulk_counts Array of how many objects were updated.
	 * @return array
	 */
	public function bulk_post_updated_messages( $bulk_messages, $bulk_counts ) {
		$bulk_messages[ Constants::ROOM_TYPE ] = [
			/* translators: %s: The room type count */
			'updated'   => _n( '%s room type updated.', '%s room types updated.', $bulk_counts['updated'], 'awebooking' ),
			/* translators: %s: The room type count */
			'locked'    => _n( '%s room type not updated, somebody is editing it.', '%s room types not updated, somebody is editing them.', $bulk_counts['locked'], 'awebooking' ),
			/* translators: %s: The room type count */
			'deleted'   => _n( '%s room type permanently deleted.', '%s room types permanently deleted.', $bulk_counts['deleted'], 'awebooking' ),
			/* translators: %s: The room type count */
			'trashed'   => _n( '%s room type moved to the Trash.', '%s room types moved to the Trash.', $bulk_counts['trashed'], 'awebooking' ),
			/* translators: %s: The room type count */
			'untrashed' => _n( '%s room type restored from the Trash.', '%s room types restored from the Trash.', $bulk_counts['untrashed'], 'awebooking' ),
		];

		$bulk_messages[ Constants::BOOKING ] = [
			/* translators: %s: The booking count */
			'updated'   => _n( '%s booking updated.', '%s bookings updated.', $bulk_counts['updated'], 'awebooking' ),
			/* translators: %s: The booking count */
			'locked'    => _n( '%s booking not updated, somebody is editing it.', '%s bookings not updated, somebody is editing them.', $bulk_counts['locked'], 'awebooking' ),
			/* translators: %s: The booking count */
			'deleted'   => _n( '%s booking permanently deleted.', '%s bookings permanently deleted.', $bulk_counts['deleted'], 'awebooking' ),
			/* translators: %s: The booking count */
			'trashed'   => _n( '%s booking moved to the Trash.', '%s bookings moved to the Trash.', $bulk_counts['trashed'], 'awebooking' ),
			/* translators: %s: The booking count */
			'untrashed' => _n( '%s booking restored from the Trash.', '%s bookings restored from the Trash.', $bulk_counts['untrashed'], 'awebooking' ),
		];

		return $bulk_messages;
	}

	/**
	 * Disable the auto-save functionality for Bookings.
	 *
	 * @access private
	 */
	public function disable_autosave() {
		global $post;

		if ( $post && Constants::BOOKING === $post->post_type ) {
			wp_dequeue_script( 'autosave' );
		}
	}

	/**
	 * Change title boxes in admin.
	 *
	 * @param  string   $text Text to shown.
	 * @param  \WP_Post $post Current post object.
	 * @return string
	 *
	 * @access private
	 */
	public function enter_title_here( $text, $post ) {
		switch ( $post->post_type ) {
			case Constants::ROOM_TYPE:
				$text = esc_html__( 'Room Type Name', 'awebooking' );
				break;
		}

		return $text;
	}

	/**
	 * Add a post display state for specified awebooking pages in the page list table.
	 *
	 * @param array    $post_states An array of post display states.
	 * @param \WP_Post $post        The current post object.
	 *
	 * @return array
	 * @access private
	 */
	public function display_post_states( $post_states, $post ) {
		switch ( true ) {
			case ( abrs_get_page_id( 'search_results' ) === $post->ID ):
				$post_states['abrs_page_check_availability'] = esc_html_x( 'Query Results', 'Page states', 'awebooking' );
				break;

			case ( abrs_get_page_id( 'checkout' ) === $post->ID ):
				$post_states['abrs_page_checkout'] = esc_html_x( 'Checkout', 'Page states', 'awebooking' );
				break;
		}

		return $post_states;
	}
}
