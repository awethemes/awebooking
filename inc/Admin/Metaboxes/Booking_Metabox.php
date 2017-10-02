<?php
namespace AweBooking\Admin\Metaboxes;

use AweBooking\Factory;
use AweBooking\AweBooking;
use AweBooking\Booking\Booking;
use AweBooking\Admin\Forms\Booking_General_From;
use AweBooking\Support\Carbonate;
use AweBooking\Support\Mailer;
use AweBooking\Notification\Booking_Cancelled;
use AweBooking\Notification\Booking_Processing;
use AweBooking\Notification\Booking_Completed;

class Booking_Metabox extends Post_Type_Metabox {
	/**
	 * Post type ID to register meta-boxes.
	 *
	 * @var string
	 */
	protected $post_type = 'awebooking';

	/**
	 * Constructor of class.
	 */
	public function __construct() {
		parent::__construct();

		// Register metaboxes.
		$this->register_customer_metabox();

		// Register/un-register metaboxes.
		add_action( 'add_meta_boxes', array( $this, 'handler_meta_boxes' ), 10 );
		add_action( 'edit_form_after_title', array( $this, 'booking_title' ), 10 );
		add_action( 'admin_footer', [ $this, 'booking_templates' ] );
	}

	/**
	 * Save CPT metadata when a custom post is saved.
	 *
	 * @access private
	 *
	 * @param int  $post_id The post ID.
	 * @param post $post    The post object.
	 * @param bool $update  Whether this is an existing post being updated or not.
	 */
	public function doing_save( $post_id, $post, $update ) {
		// If this is just a revision, don't do anything.
		if ( wp_is_post_revision( $post_id ) ) {
			return;
		}

		if ( empty( $_POST ) ) {
			return;
		}

		$booking = Factory::get_booking( $post_id );

		try {
			$sanitized = (new Booking_General_From( $booking ))->handle( $_POST );
			$datetime = Carbonate::create_datetime( $sanitized['booking_created_date'] );

			$booking['date_created'] = $datetime->toDateTimeString();
			$booking['status']       = $sanitized['booking_status'];
			$booking['customer_id']  = isset( $sanitized['booking_customer'] ) ? (int) $sanitized['booking_customer'] : 0;
		} catch ( \Exception $e ) {
			// ...
		}

		// Set checked-in, checked-out.
		$booking['checked_in']  = ( isset( $_POST['booking_checked_in'] ) && $_POST['booking_checked_in'] );
		$booking['checked_out'] = ( isset( $_POST['booking_checked_out'] ) && $_POST['booking_checked_out'] );
		$booking['featured']    = ( isset( $_POST['booking_featured'] ) && $_POST['booking_featured'] );

		// Save the booking.
		$booking->save();

		$this->proccess_booking_actions( $booking );
	}

	/**
	 * Handler booking actions.
	 *
	 * @param  Booking $booking //.
	 * @return void
	 */
	protected function proccess_booking_actions( Booking $booking ) {
		// Handle button actions.
		if ( empty( $_POST['awebooking_action'] ) ) {
			return;
		}

		$action = $_POST['awebooking_action'];

		if ( strstr( $action, 'send_email_' ) ) {
			// Load mailer.
			$email_to_send = str_replace( 'send_email_', '', $action );

			try {
				switch ( $email_to_send ) {
					case 'cancelled_order':
						Mailer::to( $booking->get_customer_email() )->send( new Booking_Cancelled( $booking ) );
						break;

					case 'customer_processing_order':
						Mailer::to( $booking->get_customer_email() )->send( new Booking_Processing( $booking ) );
						break;

					case 'customer_completed_order':
						Mailer::to( $booking->get_customer_email() )->send( new Booking_Completed( $booking ) );
						break;
				}
			} catch ( \Exception $e ) {
				// ...
			}
		}
	}

	/**
	 * Remove some un-used meta boxes.
	 */
	public function handler_meta_boxes() {
		remove_meta_box( 'slugdiv', $this->post_type, 'normal' );
		remove_meta_box( 'submitdiv', $this->post_type, 'side' );
		remove_meta_box( 'commentstatusdiv', $this->post_type, 'normal' );

		add_meta_box( 'awebooking_booking_action', esc_html__( 'Booking Action', 'awebooking' ), [ $this, 'output_action_metabox' ], AweBooking::BOOKING, 'side', 'high' );
		add_meta_box( 'awebooking-booking-notes', esc_html__( 'Booking notes', 'awebooking' ), [ $this, 'booking_note_output' ], AweBooking::BOOKING, 'side', 'default' );
	}

	public function booking_templates() {
		$screen = get_current_screen();

		if ( $this->is_current_screen() ) {
			include trailingslashit( __DIR__ ) . 'views/html-booking-templates.php';
		}
	}

	/**
	 * Prints the booking title.
	 *
	 * @access private
	 *
	 * @param  WP_Post $post WP_Post instance.
	 * @return void
	 */
	public function booking_title( $post ) {
		if ( AweBooking::BOOKING !== $post->post_type ) {
			return;
		}

		$the_booking = Factory::get_booking( $post );
		include trailingslashit( __DIR__ ) . 'views/html-booking.php';
	}

	/**
	 * Output the action metabox.
	 *
	 * @param WP_Post $post WP_Post object instance.
	 */
	public function output_action_metabox( $post ) {
		include trailingslashit( __DIR__ ) . 'views/html-booking-action.php';
	}

	/**
	 * Add customer meta box to this post type.
	 *
	 * @access private
	 *
	 * @return void
	 */
	public function register_customer_metabox() {
		$metabox = $this->create_metabox( 'awebooking_booking_general', [
			'title'  => esc_html__( 'Details', 'awebooking' ),
			'closed' => false,
		]);

		$customer = $metabox->add_section( 'customer_infomation', [
			'title' => esc_html__( 'Customer', 'awebooking' ),
		]);

		$customer->add_field( array(
			'id'               => '_customer_title',
			'type'             => 'select',
			'name'             => esc_html__( 'Title', 'awebooking' ),
			'options'          => awebooking_get_common_titles(),
			'show_option_none' => '---',
		));

		$customer->add_field( array(
			'id'   => '_customer_first_name',
			'type' => 'text',
			'name' => esc_html__( 'First name', 'awebooking' ),
			'validate' => 'required',
		));

		$customer->add_field( array(
			'id'   => '_customer_last_name',
			'type' => 'text',
			'name' => esc_html__( 'Last name', 'awebooking' ),
			'validate' => 'required',
		));

		$customer->add_field( array(
			'id'   => '_customer_address',
			'type' => 'text',
			'name' => esc_html__( 'Address', 'awebooking' ),
		));

		$customer->add_field( array(
			'id'   => '_customer_address2',
			'type' => 'text',
			'name' => esc_html__( 'Address 2', 'awebooking' ),
		));

		$customer->add_field( array(
			'id'   => '_customer_city',
			'type' => 'text',
			'name' => esc_html__( 'City', 'awebooking' ),
		));

		$customer->add_field( array(
			'id'   => '_customer_state',
			'type' => 'text',
			'name' => esc_html__( 'State', 'awebooking' ),
		));

		$customer->add_field( array(
			'id'   => '_customer_postal_code',
			'type' => 'text',
			'name' => esc_html__( 'Postal code', 'awebooking' ),
		));

		$customer->add_field( array(
			'id'   => '_customer_country',
			'type' => 'text',
			'name' => esc_html__( 'Country', 'awebooking' ),
		));

		$customer->add_field( array(
			'id'   => '_customer_phone',
			'type' => 'text',
			'name' => esc_html__( 'Phone number', 'awebooking' ),
			'validate' => 'required',
		));

		$customer->add_field( array(
			'id'   => '_customer_email',
			'type' => 'text',
			'name' => esc_html__( 'Email address', 'awebooking' ),
			'validate' => 'email',
		));

		$customer->add_field( array(
			'id'   => '_customer_company',
			'type' => 'text',
			'name' => esc_html__( 'Company', 'awebooking' ),
		));

		// Payment section.
		$payment = $metabox->add_section( 'payment', [
			'title' => esc_html__( 'Payment', 'awebooking' ),
		]);

		/*$payment->add_field( array(
			'type'            => 'select',
			'id'              => '_payment_method',
			'name'            => esc_html__( 'Payment method', 'awebooking' ),
		));*/

		$payment->add_field( array(
			'type' => 'text',
			'id'   => '_transaction_id',
			'name' => esc_html__( 'Transaction ID', 'awebooking' ),
		));

		$payment->add_field( array(
			'id'              => '_customer_note',
			'type'            => 'textarea',
			'name'            => esc_html__( 'Customer provided note:', 'awebooking' ),
			'sanitization_cb' => 'sanitize_textarea_field',
		));

		do_action( 'awebooking/booking/register_metabox_fields', $metabox, $this );
	}

	/**
	 * Output the metabox.
	 */
	public function booking_note_output() {
		global $post;

		remove_filter( 'comments_clauses', array( $this, '_exclude_booking_comments' ), 10, 1 );

		$notes = get_comments( array(
			'post_id'   => $post->ID,
			'orderby'   => 'comment_ID',
			'order'     => 'DESC',
			'approve'   => 'approve',
			'type'      => 'booking_note',
		) );

		add_filter( 'comments_clauses', array( $this, '_exclude_booking_comments' ), 10, 1 );

		include trailingslashit( __DIR__ ) . 'views/html-booking-notes.php';
	}

	/**
	 * Exclude booking comments from queries and RSS.
	 *
	 * @param  array $clauses clauses.
	 * @return array
	 */
	public function _exclude_booking_comments( $clauses ) {
		$clauses['where'] .= ( $clauses['where'] ? ' AND ' : '' ) . " comment_type != 'booking_note' ";

		return $clauses;
	}
}
