<?php

namespace AweBooking\Admin\Settings;

class Email_Setting extends Abstract_Setting {
	/**
	 * {@inheritdoc}
	 */
	protected function setup() {
		$this->form_id  = 'email';
		$this->label    = esc_html__( 'Emails', 'awebooking' );
		$this->priority = 50;
	}

	/**
	 * {@inheritdoc}
	 */
	public function setup_fields() {
		// Setup email templates settings.
		foreach ( awebooking( 'mailer' )->all() as $gateway ) {
			$this->setup_email_fields( $gateway );
		}

		$options = $this->add_section( 'email-options', [
			'title'      => esc_html__( 'Email Options', 'awebooking' ),
			'priority'   => 0,
		]);

		$options->add_field([
			'id'         => '__title_email_templates',
			'type'       => 'title',
			'name'       => esc_html__( 'Email templates', 'awebooking' ),
		]);

		$options->add_field([
			'id'         => '__email_templates',
			'type'       => 'include',
			'name'       => esc_html__( 'Email templates', 'awebooking' ),
			'include'    => trailingslashit( dirname( __DIR__ ) ) . 'views/settings/html-email-templates.php',
			'show_names' => false,
		]);

		$options->add_field([
			'id'         => '__email_sender__',
			'type'       => 'title',
			'name'       => esc_html__( 'Email sender', 'awebooking' ),
		]);

		$options->add_field([
			'id'         => 'email_from_name',
			'type'       => 'text',
			'name'       => esc_html__( '"From" name', 'awebooking' ),
			'desc'       => esc_html__( 'How the sender name appears in outgoing AweBooking emails.', 'awebooking' ),
			'tooltip'    => true,
			'default_cb' => function() {
				return esc_attr( get_bloginfo( 'name', 'display' ) );
			},
		]);

		$options->add_field([
			'id'       => 'email_from_address',
			'type'     => 'text',
			'name'     => esc_html__( '"From" address', 'awebooking' ),
			'desc'     => esc_html__( 'How the sender email appears in outgoing AweBooking emails.', 'awebooking' ),
			'default'  => esc_attr( get_option( 'admin_email' ) ),
			'tooltip'  => true,
		]);

		// Template.
		$options->add_field([
			'id'   => '__email__',
			'type' => 'title',
			'name' => esc_html__( 'Email Template', 'awebooking' ),
			/* translators: %s Preview Email Link */
			'desc' => esc_html__( 'This section lets you customize the AweBooking emails.', 'awebooking' ) . ' ' . sprintf( abrs_esc_text( __( 'Click <a href="%s" target="_blank">here</a> to preview your email template.', 'awebooking' ) ), esc_url( abrs_admin_route( '/preview-email' ) ) ),
		]);

		$options->add_field([
			'id'           => 'email_header_image',
			'type'         => 'file',
			'name'         => esc_html__( 'Header image', 'awebooking' ),
			'query_args'   => [ 'type' => 'image' ],
			'text'         => [ 'add_upload_file_text' => esc_html__( 'Choose Image', 'awebooking' ) ],
			'preview_size' => 'thumbnail',
		]);

		$options->add_field([
			'id'      => 'email_copyright',
			'type'    => 'textarea',
			'name'    => esc_html__( 'Footer Copyright', 'awebooking' ),
			'desc'    => esc_html__( 'Available placeholders: {site_title}', 'awebooking' ),
			'attributes' => [ 'style' => 'height: 80px' ],
		]);

		$options->add_field([
			'name'    => esc_html__( 'Main Color', 'awebooking' ),
			'id'      => 'email_base_color',
			'type'    => 'colorpicker',
			'default' => '#2196f3',
		]);

		$options->add_field([
			'name'    => esc_html__( 'Text Color', 'awebooking' ),
			'id'      => 'email_body_text_color',
			'type'    => 'colorpicker',
			'default' => '#74787E',
		]);

		$options->add_field([
			'name'    => esc_html__( 'Content Background', 'awebooking' ),
			'id'      => 'email_bg_color',
			'type'    => 'colorpicker',
			'default' => '#ffffff',
		]);

		$options->add_field([
			'name'    => esc_html__( 'Body Background', 'awebooking' ),
			'id'      => 'email_body_bg_color',
			'type'    => 'colorpicker',
			'default' => '#f7f7f7',
		]);
	}

	/**
	 * Setup email template fields.
	 *
	 * @param  \AweBooking\Email\Mailable $template Email template.
	 * @return void
	 */
	protected function setup_email_fields( $template ) {
		$prefix = sanitize_key( 'email_' . $template->get_id() );

		$section = $this->add_section( $template->get_id(), [
			'title' => $template->get_title(),
		]);

		$section->add_field([
			'id'    => '__title_' . $prefix,
			'type'  => 'title',
			'name'  => $template->get_title(),
			'desc'  => $template->get_description(),
		]);

		foreach ( $template->get_setting_fields() as $key => $args ) {
			$section->add_field(
				array_merge( $args, [ 'id' => $prefix . '_' . $key ] )
			);
		}
	}

	/**
	 * {@inheritdoc}
	 */
	protected function output_nav_sections() {}
}
