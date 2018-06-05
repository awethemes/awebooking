<?php
namespace AweBooking\Admin\Providers;

use AweBooking\Constants;
use AweBooking\Support\Service_Provider;

class Metaboxes_Service_Provider extends Service_Provider {
	/**
	 * Registers services on the plugin.
	 *
	 * @access private
	 */
	public function register() {
		foreach ([ // @codingStandardsIgnoreLine
			'metabox.room_type'        => \AweBooking\Admin\Metaboxes\Room_Type_Metabox::class,
			'metabox.room_type_hotel'  => \AweBooking\Admin\Metaboxes\Room_Type_Hotel_Metabox::class,
			'metabox.booking_main'     => \AweBooking\Admin\Metaboxes\Booking_Main_Metabox::class,
			'metabox.booking_rooms'    => \AweBooking\Admin\Metaboxes\Booking_Rooms_Metabox::class,
			'metabox.booking_payments' => \AweBooking\Admin\Metaboxes\Booking_Payments_Metabox::class,
			'metabox.booking_actions'  => \AweBooking\Admin\Metaboxes\Booking_Actions_Metabox::class,
			'metabox.booking_notes'    => \AweBooking\Admin\Metaboxes\Booking_Notes_Metabox::class,
			'metabox.booking_calendar' => \AweBooking\Admin\Metaboxes\Booking_Calendar_Metabox::class,
			'metabox.hotel_info'       => \AweBooking\Admin\Metaboxes\Hotel_Info_Metabox::class,
		] as $abstract => $concrete ) {
			$this->plugin->bind( $abstract, $concrete );
			$this->plugin->tag( $abstract, 'metaboxes' );
		}
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
	 * Make a callable for metabox output.
	 *
	 * @param  string $binding The binding in the plugin.
	 * @return array
	 */
	protected function metaboxcb( $binding ) {
		return [ $this->plugin->make( $binding ), 'output' ];
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
		if ( ! function_exists( 'post_categories_meta_box' ) ) {
			require_once ABSPATH . 'wp-admin/includes/meta-boxes.php';
		}

		add_meta_box( 'awebooking-booking-data', esc_html__( 'Booking Data', 'awebooking' ), $this->metaboxcb( 'metabox.booking_main' ), Constants::BOOKING, 'normal', 'high' );
		add_meta_box( 'awebooking-booking-rooms', esc_html__( 'Booking Rooms', 'awebooking' ), $this->metaboxcb( 'metabox.booking_rooms' ), Constants::BOOKING, 'normal' );
		add_meta_box( 'awebooking-booking-payments', esc_html__( 'Booking Payments', 'awebooking' ), $this->metaboxcb( 'metabox.booking_payments' ), Constants::BOOKING, 'normal' );
		add_meta_box( 'awebooking-booking-actions', esc_html__( 'Actions', 'awebooking' ), $this->metaboxcb( 'metabox.booking_actions' ), Constants::BOOKING, 'side', 'high' );
		add_meta_box( 'awebooking-booking-notes', esc_html__( 'Notes', 'awebooking' ), $this->metaboxcb( 'metabox.booking_notes' ), Constants::BOOKING, 'side', 'default' );

		add_meta_box( 'awebooking-room-type-data', esc_html__( 'Room Type Data', 'awebooking' ), $this->metaboxcb( 'metabox.room_type' ), Constants::ROOM_TYPE, 'normal' );
		add_meta_box( 'awebooking-room-type-hotel', esc_html__( 'Hotel location', 'awebooking' ), $this->metaboxcb( 'metabox.room_type_hotel' ), Constants::ROOM_TYPE, 'side' );

		add_meta_box( 'awebooking-hotel-info', esc_html__( 'Hotel Information', 'awebooking' ), $this->metaboxcb( 'metabox.hotel_info' ), Constants::HOTEL_LOCATION, 'normal' );
	}

	/**
	 * Check if we're saving, the trigger an action based on the post type.
	 *
	 * @param int      $post_id The post ID.
	 * @param \WP_Post $post    The WP_Post object instance.
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
		$request = $this->plugin->make( 'request' );

		// Call the proccess based on post_type.
		switch ( $post->post_type ) {
			case 'room_type':
				$this->plugin->make( 'metabox.room_type' )->save( $post, $request );
				break;

			case 'hotel_location':
				$this->plugin->make( 'metabox.hotel_info' )->save( $post, $request );
				break;

			case 'awebooking':
				$this->plugin->make( 'metabox.booking_main' )->save( $post, $request );
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
