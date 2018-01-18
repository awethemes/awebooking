<?php
namespace AweBooking\Admin\Settings;

use AweBooking\Admin\Admin_Settings;

class Email_Setting extends Abstract_Setting {
	/**
	 * {@inheritdoc}
	 */
	public function registers( Admin_Settings $settings ) {
		$email = $settings->add_panel( 'email', [
			'title'    => esc_html__( 'Email', 'awebooking' ),
			'priority' => 30,
			'capability' => 'manage_awebooking',
		]);

		$email_general = $settings->add_section( 'email-general', [
			'title'    => esc_html__( 'Email Sender', 'awebooking' ),
			'priority' => 0,
			'capability' => 'manage_awebooking',
		])->as_child_of( $email );

		$admin_new_booking = $settings->add_section( 'admin-new-booking', [
			'title'    => esc_html__( 'New Booking (Admin)', 'awebooking' ),
			'priority' => 10,
		])->as_child_of( $email );

		$new_booking = $settings->add_section( 'email-new-booking', [
			'title'    => esc_html__( 'New Booking', 'awebooking' ),
			'priority' => 20,
		])->as_child_of( $email );

		$cancelled_booking = $settings->add_section( 'email-cancelled-booking', [
			'title'    => esc_html__( 'Cancelled Booking', 'awebooking' ),
			'priority' => 30,
		])->as_child_of( $email );

		$processing_booking = $settings->add_section( 'email-processing-booking', [
			'title'    => esc_html__( 'Processing Booking', 'awebooking' ),
			'priority' => 40,
		])->as_child_of( $email );

		$completed_booking = $settings->add_section( 'email-completed-booking', [
			'title'    => esc_html__( 'Completed Booking', 'awebooking' ),
			'priority' => 50,
		])->as_child_of( $email );

		$this->register_general_settings( $email_general );
		$this->register_new_booking_settings( $new_booking );
		$this->register_admin_new_booking_settings( $admin_new_booking );
		$this->register_cancelled_booking_settings( $cancelled_booking );
		$this->register_processing_booking_settings( $processing_booking );
		$this->register_completed_booking_settings( $completed_booking );
	}

	/**
	 * Register email general settings.
	 *
	 * @param  Skeleton\CMB2\Section $email_general Section instance.
	 * @return void
	 */
	protected function register_general_settings( $email_general ) {
		$email_general->add_field([
			'id'   => '__email_sender__',
			'type' => 'title',
			'name' => esc_html__( 'Email sender', 'awebooking' ),
		]);

		$email_general->add_field([
			'id'       => 'email_from_name',
			'type'     => 'text',
			'name'     => esc_html__( '"From" name', 'awebooking' ),
			'default'  => awebooking( 'setting' )->get_default( 'email_from_name' ),
		]);

		$email_general->add_field([
			'id'       => 'email_from_address',
			'type'     => 'text',
			'name'     => esc_html__( '"From" address', 'awebooking' ),
			'default'  => awebooking( 'setting' )->get_default( 'email_from_address' ),
			'validate' => 'email',
		]);

		// ...
		$email_general->add_field( array(
			'id'   => '__email__',
			'type' => 'title',
			'name' => esc_html__( 'Email Settings', 'awebooking' ),
		) );

		$email_general->add_field( array(
			'name'    => esc_html__( 'Base Color', 'awebooking' ),
			'id'      => 'email_base_color',
			'type'    => 'colorpicker',
			'default' => awebooking( 'setting' )->get_default( 'email_base_color' ),
		) );

		$email_general->add_field( array(
			'name'    => esc_html__( 'Body Background Color', 'awebooking' ),
			'id'      => 'email_bg_color',
			'type'    => 'colorpicker',
			'default' => awebooking( 'setting' )->get_default( 'email_bg_color' ),
		) );

		$email_general->add_field( array(
			'name'    => esc_html__( 'Email Background Color', 'awebooking' ),
			'id'      => 'email_body_bg_color',
			'type'    => 'colorpicker',
			'default' => awebooking( 'setting' )->get_default( 'email_body_bg_color' ),
		) );

		$email_general->add_field( array(
			'name'    => esc_html__( 'Email Body Text Color', 'awebooking' ),
			'id'      => 'email_body_text_color',
			'type'    => 'colorpicker',
			'default' => awebooking( 'setting' )->get_default( 'email_body_text_color' ),
		) );

		$email_general->add_field( array(
			'name'    => esc_html__( 'Email Copyright', 'awebooking' ),
			'id'      => 'email_copyright',
			'type'    => 'text',
			'default' => awebooking( 'setting' )->get_default( 'email_copyright' ),
		) );

		$email_general->add_field( array(
			'name'    => esc_html__( 'Admin notify', 'awebooking' ),
			'id'      => 'email_admin_notify',
			'type'    => 'toggle',
			'desc'    => esc_html__( 'Send to email address from General Settings', 'awebooking' ),
			'default' => awebooking( 'setting' )->get_default( 'email_admin_notify' ),
		) );

		$email_general->add_field( array(
			'name'    => esc_html__( 'Notifications to other emails', 'awebooking' ),
			'id'      => 'email_notify_another_emails',
			'type'    => 'text',
			'desc'    => esc_html__( 'Enter some emails by "," separating values.', 'awebooking' ),
		) );
	}

	/**
	 * Register email new booking (admin) settings.
	 *
	 * @param  Skeleton\CMB2\Section $new_booking Section instance.
	 * @return void
	 */
	protected function register_admin_new_booking_settings( $admin_new_booking ) {
		$admin_new_booking->add_field([
			'id'   => '__email_admin_new_booking__',
			'type' => 'title',
			'desc' => sprintf( esc_html__( 'Email settings for new booking for admin. Click %s to preview.', 'awebooking' ), '<a href="' . esc_url( admin_url( '?page=awebooking-email-preview&status=admin_new' ) ) . '" target="_blank">here</a>' ),
			'name' => esc_html__( 'New booking', 'awebooking' ),
		]);

		$admin_new_booking->add_field([
			'name'    => esc_html__( 'Email subject', 'awebooking' ),
			'id'      => 'email_admin_new_subject',
			'type'    => 'textarea_small',
			'default' => 'You have new booking #{order_number} - {order_date}',
			'attributes' => array( 'style' => 'height:50px;' ),
		]);

		/*$admin_new_booking->add_field([
			'name'    => esc_html__( 'Email header', 'awebooking' ),
			'id'      => 'email_admin_new_header',
			'type'    => 'text',
			'default' => esc_html__( 'New customer booking', 'awebooking' ),
		]);*/

		$admin_new_booking->add_field([
			'name'    => esc_html__( 'Email content', 'awebooking' ),
			'id'      => 'email_admin_new_content',
			'type'    => 'wysiwyg',
			'default' => awebooking( 'setting' )->get_default( 'email_admin_new_content' ),
			'after'   => $this->get_shortcodes_notes(),
			'options' => array(
				'media_buttons' => false,
				'tinymce' => false,
			),
		]);
	}

	/**
	 * Register email new booking settings.
	 *
	 * @param  Skeleton\CMB2\Section $new_booking Section instance.
	 * @return void
	 */
	protected function register_new_booking_settings( $new_booking ) {
		$new_booking->add_field( array(
			'id'   => '__email_new_booking__',
			'type' => 'title',
			'desc' => sprintf( esc_html__( 'Email settings for new booking. Click %s to preview.', 'awebooking' ), '<a href="' . esc_url( admin_url( '?page=awebooking-email-preview&status=new' ) ) . '" target="_blank">here</a>' ),
			'name' => esc_html__( 'New booking', 'awebooking' ),
		) );

		$new_booking->add_field( array(
			'name'    => esc_html__( 'Enable?', 'awebooking' ),
			'id'      => 'email_new_enable',
			'type'    => 'toggle',
			'desc'    => esc_html__( 'Check to turn on email notification for new booking', 'awebooking' ),
			'default' => awebooking( 'setting' )->get_default( 'email_new_enable' ),
		) );

		$new_booking->add_field( array(
			'name'    => esc_html__( 'Email subject', 'awebooking' ),
			'id'      => 'email_new_subject',
			'type'    => 'textarea_small',
			'default' => '[{site_title}] New customer booking #{order_number} - {order_date}',
			'desc'    => esc_html__( 'This controls the email subject line. Leave blank to use the default subject', 'awebooking' ) . ': [{site_title}] New customer booking ({order_number}) - {order_date}',
			'attributes' => array( 'style' => 'height:50px;' ),
		) );

		$new_booking->add_field( array(
			'name'    => esc_html__( 'Email header', 'awebooking' ),
			'id'      => 'email_new_header',
			'type'    => 'text',
			'default' => esc_html__( 'New customer booking', 'awebooking' ),
			'desc'    => esc_html__( 'This controls the main heading contained within the email notification. Leave blank to use the default heading: Your booking is completed', 'awebooking' ),
		) );

		$new_booking->add_field( array(
			'name'    => esc_html__( 'Email content', 'awebooking' ),
			'id'      => 'email_new_content',
			'type'    => 'wysiwyg',
			'default' => awebooking( 'setting' )->get_default( 'email_new_content' ),
			'after'   => $this->get_shortcodes_notes(),
			'options' => array(
				'media_buttons' => false,
				'tinymce' => false,
			),
		) );
	}

	/**
	 * Register email cancelled booking settings.
	 *
	 * @param  Skeleton\CMB2\Section $cancelled_booking Section instance.
	 * @return void
	 */
	protected function register_cancelled_booking_settings( $cancelled_booking ) {
		$cancelled_booking->add_field( array(
			'id'   => '__email_cancelled_booking__',
			'type' => 'title',
			'desc' => sprintf( esc_html__( 'Email settings for cancelled booking. Click %s to preview.', 'awebooking' ), '<a href="' . esc_url( admin_url( '?page=awebooking-email-preview&status=cancelled' ) ) . '" target="_blank">here</a>' ),
			'name' => esc_html__( 'Cancelled booking', 'awebooking' ),
		) );

		$cancelled_booking->add_field( array(
			'name'    => esc_html__( 'Enable?', 'awebooking' ),
			'id'      => 'email_cancelled_enable',
			'type'    => 'toggle',
			'desc'    => esc_html__( 'Check to turn on email notification for cancelled booking', 'awebooking' ),
			'default' => awebooking( 'setting' )->get_default( 'email_cancelled_enable' ),
		) );

		$cancelled_booking->add_field( array(
			'name'    => esc_html__( 'Email subject', 'awebooking' ),
			'id'      => 'email_cancelled_subject',
			'type'    => 'textarea_small',
			'default' => 'Your {site_title} booking receipt from {order_date}',
			'desc'    => esc_html__( 'This controls the email subject line. Leave blank to use the default subject', 'awebooking' ) . ': Your {site_title} booking receipt from {order_date}',
			'attributes' => array( 'style' => 'height:50px;' ),
		) );

		$cancelled_booking->add_field( array(
			'name'    => esc_html__( 'Email header', 'awebooking' ),
			'id'      => 'email_cancelled_header',
			'type'    => 'text',
			'default' => esc_html__( 'Your booking is cancelled', 'awebooking' ),
			'desc'    => esc_html__( 'This controls the main heading contained within the email notification. Leave blank to use the default heading: Thank you for your booking.', 'awebooking' ),
		) );

		$cancelled_booking->add_field( array(
			'name'    => esc_html__( 'Email content', 'awebooking' ),
			'id'      => 'email_cancelled_content',
			'type'    => 'wysiwyg',
			'default' => awebooking( 'setting' )->get_default( 'email_cancelled_content' ),
			'after'   => $this->get_shortcodes_notes(),
			'options' => array(
				'media_buttons' => false,
				'tinymce' => false,
			),
		) );
	}

	/**
	 * Register email processing booking settings.
	 *
	 * @param  Skeleton\CMB2\Section $processing_booking Section instance.
	 * @return void
	 */
	protected function register_processing_booking_settings( $processing_booking ) {
		$processing_booking->add_field( array(
			'id'   => '__email_processing_booking__',
			'type' => 'title',
			'name' => esc_html__( 'Processing booking', 'awebooking' ),
			'desc' => sprintf( esc_html__( 'Email settings for processing booking. Click %s to preview.', 'awebooking' ), '<a href="' . esc_url( admin_url( '?page=awebooking-email-preview&status=processing' ) ) . '" target="_blank">here</a>' ),
		) );

		$processing_booking->add_field( array(
			'name'    => esc_html__( 'Enable', 'awebooking' ),
			'id'      => 'email_processing_enable',
			'type'    => 'toggle',
			'desc'    => esc_html__( 'Check to turn on email notification for processing booking', 'awebooking' ),
			'default' => awebooking( 'setting' )->get_default( 'email_processing_enable' ),
		) );

		$processing_booking->add_field( array(
			'name'    => esc_html__( 'Email subject', 'awebooking' ),
			'id'      => 'email_processing_subject',
			'type'    => 'textarea_small',
			'default' => __( '[{site_title}] Processing your booking #{order_number} - {order_date}', 'awebooking' ),
			'desc'    => esc_html__( 'This controls the email subject line. Leave blank to use the default subject', 'awebooking' ) . ': Your {site_title} booking from {order_date} is being processed.',
			'attributes' => array( 'style' => 'height:50px;' ),
		) );

		$processing_booking->add_field( array(
			'name'    => esc_html__( 'Email header', 'awebooking' ),
			'id'      => 'email_processing_header',
			'type'    => 'text',
			'default' => esc_html__( 'Your booking is being processed', 'awebooking' ),
			'desc'    => esc_html__( 'This controls the main heading contained within the email notification. Leave blank to use the default heading: Your booking is being processed.', 'awebooking' ),
		) );

		$processing_booking->add_field( array(
			'name'    => esc_html__( 'Email content', 'awebooking' ),
			'id'      => 'email_processing_content',
			'type'    => 'wysiwyg',
			'default' => awebooking( 'setting' )->get_default( 'email_processing_content' ),
			'after'   => $this->get_shortcodes_notes(),
			'options' => array(
				'media_buttons' => false,
				'tinymce' => false,
			),
		) );

	}

	/**
	 * Register email completed booking settings.
	 *
	 * @param  Skeleton\CMB2\Section $completed_booking Section instance.
	 * @return void
	 */
	protected function register_completed_booking_settings( $completed_booking ) {
		$completed_booking->add_field( array(
			'id'   => '__email_completed_booking__',
			'type' => 'title',
			'name' => esc_html__( 'Completed booking', 'awebooking' ),
			'desc' => sprintf( esc_html__( 'Email settings for completed booking. Click %s to preview.', 'awebooking' ), '<a href="' . esc_url( admin_url( '?page=awebooking-email-preview&status=completed' ) ) . '" target="_blank">here</a>' ),
		) );

		$completed_booking->add_field( array(
			'name'    => esc_html__( 'Enable', 'awebooking' ),
			'id'      => 'email_complete_enable',
			'type'    => 'toggle',
			'desc'    => esc_html__( 'Check to turn on email notification for completed booking', 'awebooking' ),
			'default' => awebooking( 'setting' )->get_default( 'email_complete_enable' ),
		) );

		$completed_booking->add_field( array(
			'name'    => esc_html__( 'Email subject', 'awebooking' ),
			'id'      => 'email_complete_subject',
			'type'    => 'textarea_small',
			'default' => 'Your {site_title} booking from {order_date} is completed',
			'desc'    => esc_html__( 'This controls the email subject line. Leave blank to use the default subject', 'awebooking' ) . ': Your {site_title} booking from {order_date} is completed.',
			'attributes' => array( 'style' => 'height:50px;' ),
		) );

		$completed_booking->add_field( array(
			'name'    => esc_html__( 'Email header', 'awebooking' ),
			'id'      => 'email_complete_header',
			'type'    => 'text',
			'default' => esc_html__( 'Your booking is completed', 'awebooking' ),
			'desc'    => esc_html__( 'This controls the main heading contained within the email notification. Leave blank to use the default heading: Your booking is completed.', 'awebooking' ),
		) );

		$completed_booking->add_field( array(
			'name'    => esc_html__( 'Email content', 'awebooking' ),
			'id'      => 'email_complete_content',
			'type'    => 'wysiwyg',
			'type'    => 'wysiwyg',
			'default' => awebooking( 'setting' )->get_default( 'email_complete_content' ),
			'after'   => $this->get_shortcodes_notes(),
			'options' => array(
				'tinymce'       => false,
				'media_buttons' => false,
			),
		) );
	}

	/**
	 * Get the shortcodes notes.
	 *
	 * @return string
	 */
	protected function get_shortcodes_notes() {
		$shortcodes = [
			'booking_id'           => esc_html__( 'The booking ID', 'awebooking' ),
			'created_date'         => esc_html__( 'The created date', 'awebooking' ),
			'contents'             => esc_html__( 'The booking contents', 'awebooking' ),
			'total'                => esc_html__( 'The total amount', 'awebooking' ),
			'customer_name'        => esc_html__( 'The customer name', 'awebooking' ),
			'customer_details'     => esc_html__( 'The customer details', 'awebooking' ),
			'customer name'        => esc_html__( 'The customer name', 'awebooking' ),
			'customer first_name'  => esc_html__( 'The customer first name', 'awebooking' ),
			'customer last_name'   => esc_html__( 'The customer last name', 'awebooking' ),
			'customer address'     => esc_html__( 'The customer address', 'awebooking' ),
			'customer address2'    => esc_html__( 'The customer address 2', 'awebooking' ),
			'customer city'        => esc_html__( 'The customer city', 'awebooking' ),
			'customer state'       => esc_html__( 'The customer state', 'awebooking' ),
			'customer postal_code' => esc_html__( 'The customer postal code', 'awebooking' ),
			'customer country'     => esc_html__( 'The customer country', 'awebooking' ),
			'customer company'     => esc_html__( 'The customer company', 'awebooking' ),
			'customer phone'       => esc_html__( 'The customer phone', 'awebooking' ),
			'customer email'       => esc_html__( 'The customer email', 'awebooking' ),
		];

		$contents = '<div class="awebooking-sweet-alert" style="margin-top: 1em; max-width: 100%;">';
		$contents .= '<h4>' . esc_html__( 'You can use following shortcodes in content of this template', 'awebooking' ) . '</h4>';

		$contents .= '<table class="awebooking-minimal-table"><tbody>';
		foreach ( $shortcodes as $key => $value ) {
			$contents .= '<tr><td><code>{' . esc_html( $key ) . '}</code></td><td>' . esc_html( $value ) . '</td></tr>';
		}

		$contents = apply_filters( 'awebooking/after_add_email_notes', $contents );

		$contents .= '</tbody></table>';
		$contents .= '</div>';

		return $contents;
	}
}
