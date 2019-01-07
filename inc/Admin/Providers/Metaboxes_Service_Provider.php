<?php

namespace AweBooking\Admin\Providers;

use AweBooking\Constants;
use AweBooking\Admin\Metabox;
use AweBooking\Support\Service_Provider;

class Metaboxes_Service_Provider extends Service_Provider {
	/**
	 * Registers services on the plugin.
	 *
	 * @access private
	 */
	public function register() {
		foreach ([ // @codingStandardsIgnoreLine
			'metabox.room_type_data'   => \AweBooking\Admin\Metaboxes\Room_Type_Data_Metabox::class,
			'metabox.room_type_hotel'  => \AweBooking\Admin\Metaboxes\Room_Type_Hotel_Metabox::class,
			'metabox.booking_main'     => \AweBooking\Admin\Metaboxes\Booking_Main_Metabox::class,
			'metabox.booking_rooms'    => \AweBooking\Admin\Metaboxes\Booking_Items_Metabox::class,
			'metabox.booking_payments' => \AweBooking\Admin\Metaboxes\Booking_Payments_Metabox::class,
			'metabox.booking_actions'  => \AweBooking\Admin\Metaboxes\Booking_Actions_Metabox::class,
			'metabox.booking_notes'    => \AweBooking\Admin\Metaboxes\Booking_Notes_Metabox::class,
			'metabox.hotel_info'       => \AweBooking\Admin\Metaboxes\Hotel_Infomations_Metabox::class,
			'metabox.service_data'     => \AweBooking\Admin\Metaboxes\Service_Data_Metabox::class,
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

		foreach ( $this->plugin->tagged( 'metaboxes' ) as $box ) {
			if ( $box instanceof Metabox && $box->should_show() ) {
				add_meta_box( $box->id, $box->title, $box->callback(), $box->screen, $box->context, $box->priority );
			}
		}
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
		if ( empty( $post_id ) || $is_saving ) {
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
		if ( empty( $_POST['post_ID'] ) || (int) $_POST['post_ID'] !== (int) $post_id ) {
			return;
		}

		// Save the state to run once time, avoid potential endless loops.
		$is_saving = true;

		// Make the HTTP request.
		$request = $this->plugin->make( 'request' );

		// Filter boxes to perform save action.
		$current_screen = get_current_screen();

		if ( $current_screen && ! empty( $current_screen->id ) ) {
			$boxes = abrs_collect( $this->plugin->tagged( 'metaboxes' ) )
				->filter( function ( $box ) {
					return $box instanceof Metabox && method_exists( $box, 'save' );
				})
				->filter( function ( Metabox $box ) use ( $current_screen ) {
					return in_array( $current_screen->id, $box->get_screen_ids() );
				});

			// Handle save the boxes.
			foreach ( $boxes as $box ) {
				try {
					$box->save( $post, $request );
				} catch ( \Exception $e ) {
					abrs_report( $e );
				}
			}
		}

		/**
		 * Fire the process event based on current post type.
		 *
		 * @param \WP_Post                $post    The WP_Post object instance.
		 * @param \WPLibs\Http\Request $request The HTTP Request.
		 */
		do_action( "abrs_process_{$post->post_type}_meta", $post, $request );
	}
}
