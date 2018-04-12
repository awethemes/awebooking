<?php
namespace AweBooking\Admin\Providers;

use AweBooking\Constants;
use AweBooking\Support\Service_Provider;
use AweBooking\Admin\Metaboxes\Booking_Metabox;
use AweBooking\Admin\Metaboxes\Booking_Actions_Metabox;
use AweBooking\Admin\Metaboxes\Booking_Rooms_Metabox;
use AweBooking\Admin\Metaboxes\Booking_Payments_Metabox;
use AweBooking\Admin\Metaboxes\Room_Type_Metabox;
use Awethemes\Http\Request;

class Metaboxes_Service_Provider extends Service_Provider {
	/**
	 * Registers services on the plugin.
	 *
	 * @access private
	 */
	public function register() {
		$this->plugin->bind( 'metabox.booking', Booking_Metabox::class );
		$this->plugin->bind( 'metabox.booking_rooms', Booking_Rooms_Metabox::class );
		$this->plugin->bind( 'metabox.booking_payments', Booking_Payments_Metabox::class );
		$this->plugin->bind( 'metabox.booking_actions', Booking_Actions_Metabox::class );
		$this->plugin->bind( 'metabox.room_type', Room_Type_Metabox::class );
	}

	/**
	 * Init the hooks.
	 *
	 * @access private
	 */
	public function init() {
		add_action( 'save_post', [ $this, 'save_metaboxes' ], 1, 2 );
		add_action( 'add_meta_boxes', [ $this, 'remove_metaboxes' ], 5 );
		add_action( 'add_meta_boxes', [ $this, 'register_metaboxes' ], 10 );
	}

	/**
	 * Remove unnecessary metaboxes.
	 *
	 * @access private
	 */
	public function remove_metaboxes() {
		remove_meta_box( 'submitdiv', Constants::BOOKING, 'side' );
		remove_meta_box( 'slugdiv', Constants::BOOKING, 'normal' );
		remove_meta_box( 'commentsdiv', Constants::BOOKING, 'normal' );
		remove_meta_box( 'commentstatusdiv', Constants::BOOKING, 'normal' );

		remove_meta_box( 'hotel_amenitydiv', Constants::ROOM_TYPE, 'side' );
		remove_meta_box( 'hotel_extra_servicediv', Constants::ROOM_TYPE, 'side' );
	}

	/**
	 * Register metaboxes.
	 *
	 * @access private
	 */
	public function register_metaboxes() {
		// Booking meta-boxes.
		add_meta_box( 'awebooking-booking-data', esc_html__( 'Booking Data', 'awebooking' ), $this->output_metabox( 'metabox.booking' ), Constants::BOOKING, 'normal', 'high' );
		add_meta_box( 'awebooking-booking-rooms', esc_html__( 'Booking Rooms', 'awebooking' ), $this->output_metabox( 'metabox.booking_rooms' ), Constants::BOOKING, 'normal' );
		add_meta_box( 'awebooking-booking-payments', esc_html__( 'Booking Payments', 'awebooking' ), $this->output_metabox( 'metabox.booking_payments' ), Constants::BOOKING, 'normal' );
		add_meta_box( 'awebooking-booking-actions', esc_html__( 'Actions', 'awebooking' ), $this->output_metabox( 'metabox.booking_actions' ), Constants::BOOKING, 'side', 'high' );

		// Room Type meta-boxes.
		add_meta_box( 'awebooking-room-type-data', esc_html__( 'Room Type Data', 'awebooking' ), $this->output_metabox( 'metabox.room_type' ), Constants::ROOM_TYPE, 'normal' );
	}

	/**
	 * Make a callable for metabox output.
	 *
	 * @param  string $binding The binding in the plugin.
	 * @return array
	 */
	protected function output_metabox( $binding ) {
		return [ $this->plugin->make( $binding ), 'output' ];
	}

	/**
	 * Check if we're saving, the trigger an action based on the post type.
	 *
	 * @param int     $post_id The post ID.
	 * @param WP_Post $post    The WP_Post object instance.
	 *
	 * @access private
	 */
	public function save_metaboxes( $post_id, $post ) {
		static $is_saving;

		// The $post_id and $post are required.
		if ( empty( $post_id ) || empty( $post ) && $is_saving ) {
			return;
		}

		// Don't save meta-boxes for revisions or autosaves.
		if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
			|| is_int( wp_is_post_revision( $post ) )
			|| is_int( wp_is_post_autosave( $post ) ) ) {
			return;
		}

		// Check user has permission to edit.
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		// Verify the nonce.
		if ( empty( $_POST['_awebooking_nonce'] ) || ! wp_verify_nonce( $_POST['_awebooking_nonce'], 'awebooking_save_data' ) ) {
			return;
		}

		// Check the post being saved == the $post_id to
		// prevent triggering this call for other save_post events.
		if ( empty( $_POST['post_ID'] ) || $_POST['post_ID'] != $post_id ) {
			return;
		}

		// Save the state to run once time, avoid potential endless loops.
		$is_saving = true;

		// Make the HTTP request.
		$request = $this->plugin->make( Request::class );

		// Call the proccess based on post_type.
		switch ( $post->post_type ) {
			case 'room_type':
				$this->plugin->make( 'metabox.room_type' )->save( $post, $request );
				break;

			case 'awebooking':
				$this->plugin->make( 'metabox.booking' )->save( $post, $request );
				$this->plugin->make( 'metabox.booking_actions' )->save( $post, $request );
				break;
		}

		/**
		 * Fire the process event based on current post type.
		 *
		 * @param \WP_Post                $post    The WP_Post object instance.
		 * @param \Awethemes\Http\Request $request The HTTP Request.
		 */
		do_action( "awebooking/process_{$post->post_type}_meta", $post, $request );
	}
}
