<?php
namespace AweBooking\Admin\Metaboxes;

use AweBooking\Factory;
use AweBooking\AweBooking;
use AweBooking\Booking\Booking;

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
			'closed' => true,
		]);

		$customer = $metabox->add_section( 'customer_infomation', [
			'title' => esc_html__( 'Customer', 'awebooking' ),
		]);

		$customer->add_field( array(
			'id'               => 'customer_title',
			'type'             => 'select',
			'name'             => esc_html__( 'Title', 'awebooking' ),
			'options'          => awebooking_get_common_titles(),
			'show_option_none' => '---',
		));

		$customer->add_field( array(
			'id'   => 'customer_first_name',
			'type' => 'text',
			'name' => esc_html__( 'First name', 'awebooking' ),
			'validate' => 'required',
		));

		$customer->add_field( array(
			'id'   => 'customer_last_name',
			'type' => 'text',
			'name' => esc_html__( 'Last name', 'awebooking' ),
			'validate' => 'required',
		));

		$customer->add_field( array(
			'id'   => 'customer_phone',
			'type' => 'text',
			'name' => esc_html__( 'Phone number', 'awebooking' ),
			'validate' => 'required',
		));

		$customer->add_field( array(
			'id'   => 'customer_email',
			'type' => 'text',
			'name' => esc_html__( 'Email address', 'awebooking' ),
			'validate' => 'email',
		));

		$customer->add_field( array(
			'id'   => 'customer_address',
			'type' => 'text',
			'name' => esc_html__( 'Address', 'awebooking' ),
		));

		$customer->add_field( array(
			'id'   => 'customer_company',
			'type' => 'text',
			'name' => esc_html__( 'Company', 'awebooking' ),
		));

		$customer->add_field( array(
			'id'   => 'customer_note',
			'type' => 'textarea',
			'name' => esc_html__( 'Customer Notes', 'awebooking' ),
			'sanitization_cb' => 'sanitize_textarea_field',
		));
	}

	/**
	 * Output the metabox.
	 */
	public function booking_note_output() {
		global $post;

		remove_filter( 'comments_clauses', array( $this, 'exclude_booking_comments' ), 10, 1 );

		$notes = get_comments( array(
			'post_id'   => $post->ID,
			'orderby'   => 'comment_ID',
			'order'     => 'DESC',
			'approve'   => 'approve',
			'type'      => 'booking_note',
		) );

		add_filter( 'comments_clauses', array( $this, 'exclude_booking_comments' ), 10, 1 );

		include trailingslashit( __DIR__ ) . 'views/html-booking-notes.php';
	}

	/**
	 * Exclude booking comments from queries and RSS.
	 *
	 * @param  array $clauses clauses.
	 * @return array
	 */
	public function exclude_booking_comments( $clauses ) {
		$clauses['where'] .= ( $clauses['where'] ? ' AND ' : '' ) . " comment_type != 'booking_note' ";

		return $clauses;
	}
}
