<?php
namespace AweBooking\Admin\Pages;

use AweBooking\AweBooking;
use AweBooking\Support\Mailer;
use AweBooking\Notification\Booking_Created;
use AweBooking\Notification\Booking_Cancelled;
use AweBooking\Notification\Booking_Processing;
use AweBooking\Notification\Booking_Completed;
use AweBooking\Booking\Booking;

class Admin_Email_Preview {

	/** @var string current status */
	private $status   = '';

	/** @var array statuses for the email */
	private $statuses  = array();

	/**
	 * Hook in tabs.
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'admin_menus' ) );
		add_action( 'admin_init', array( $this, 'get_content_templates' ) );
	}

	/**
	 * Add admin menus/screens.
	 */
	public function admin_menus() {
		add_submenu_page( null, '', '', 'manage_options', 'awebooking-email-preview', '' );
	}

	/**
	 * Get content templates.
	 */
	public function get_content_templates() {
		if ( empty( $_GET['page'] ) || 'awebooking-email-preview' !== $_GET['page'] ) {
			return;
		}

		$default_statuses = array(
			'new' => array(
				'name'    => __( 'New Booking', 'awebooking' ),
				'view'    => [ $this, 'get_new_email_preview_template' ],
			),
			'cancelled' => array(
				'name'    => __( 'Cancelled Booking', 'awebooking' ),
				'view'    => [ $this, 'get_cancelled_email_preview_template' ],
			),
			'processing' => array(
				'name'    => __( 'Processing Booking', 'awebooking' ),
				'view'    => [ $this, 'get_processing_email_preview_template' ],
			),
			'completed' => array(
				'name'    => __( 'Completed Booking', 'awebooking' ),
				'view'    => [ $this, 'get_completed_email_preview_template' ],
			),
		);

		$this->statuses = apply_filters( 'awebooking/email_preview_statuses', $default_statuses );
		$this->status = isset( $_GET['status'] ) ? sanitize_key( $_GET['status'] ) : current( array_keys( $this->statuses ) );
		ob_start();
		$this->get_content_email_template();
		exit;
	}

	/**
	 * Output the content for the current status.
	 */
	public function get_content_email_template() {
		echo '<div class="awebooking-email-preview">';
		call_user_func( $this->statuses[ $this->status ]['view'], $this );
		echo '</div>';
	}

	/**
	 * Get new email template.
	 */
	public function get_new_email_preview_template() {
		$booking = new Booking( 0 );
		$booking['date_created'] = '2017-08-21 07:20:09';
		try {
			$booking_created = new Booking_Created( $booking );
			$booking_created->set_dummy( true );
			print $booking_created->message(); // WPCS: xss ok.
		} catch ( \Exception $e ) {
			// ...
		}
	}

	/**
	 * Get cancelled email template.
	 */
	public function get_cancelled_email_preview_template() {
		$booking = new Booking( 0 );
		$booking['date_created'] = '2017-08-21 07:20:09';
		try {
			$booking_created = new Booking_Cancelled( $booking );
			$booking_created->set_dummy( true );
			print $booking_created->message(); // WPCS: xss ok.
		} catch ( \Exception $e ) {
			// ...
		}
	}

	/**
	 * Get cancelled email template.
	 */
	public function get_processing_email_preview_template() {
		$booking = new Booking( 0 );
		$booking['date_created'] = '2017-08-21 07:20:09';
		try {
			$booking_created = new Booking_Processing( $booking );
			$booking_created->set_dummy( true );
			print $booking_created->message(); // WPCS: xss ok.
		} catch ( \Exception $e ) {
			// ...
		}
	}

	/**
	 * Get completed email template.
	 */
	public function get_completed_email_preview_template() {
		$booking = new Booking( 0 );
		$booking['date_created'] = '2017-08-21 07:20:09';
		try {
			$booking_created = new Booking_Completed( $booking );
			$booking_created->set_dummy( true );
			print $booking_created->message(); // WPCS: xss ok.
		} catch ( \Exception $e ) {
			// ...
		}
	}
}
