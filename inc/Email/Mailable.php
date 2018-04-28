<?php
namespace AweBooking\Email;

use Pelago\Emogrifier;

abstract class Mailable {
	/**
	 * Email template ID.
	 *
	 * @var string
	 */
	protected $id;

	/**
	 * Email template title.
	 *
	 * @var string
	 */
	public $title = '';

	/**
	 * Description for the email template.
	 *
	 * @var string
	 */
	public $description = '';

	/**
	 * If the template is enabled.
	 *
	 * @var bool|string (off or on)
	 */
	public $enabled = true;

	/**
	 * Recipient(s) for the email.
	 *
	 * @var string
	 */
	public $recipient = '';

	/**
	 * An array to find/replace in subjects or body.
	 *
	 * @var array
	 */
	protected $placeholders = [];

	/**
	 * The admin setting fields.
	 *
	 * @var array
	 */
	protected $setting_fields = [];

	/**
	 * Styling for mailable.
	 *
	 * @var string
	 */
	protected $style = 'default.css';

	/**
	 * Default layout for
	 *
	 * @var string
	 */
	protected $layout = 'layout.php';

	/**
	 * Constructor.
	 */
	public function __construct() {
		// $this->setup();
		$this->setup_fields();
	}

	/**
	 * Get template ID.
	 *
	 * @return string
	 */
	public function get_id() {
		return $this->id;
	}

	/**
	 * Gets the email template title.
	 *
	 * @return string
	 */
	public function get_title() {
		return apply_filters( 'awebooking/email/get_title', $this->title, $this );
	}

	/**
	 * Gets the email template description.
	 *
	 * @return string
	 */
	public function get_description() {
		return apply_filters( 'awebooking/email/get_description', $this->description, $this );
	}

	/**
	 * Get the setting fields.
	 *
	 * @return array|null
	 */
	public function get_setting_fields() {
		return apply_filters( 'awebooking/email/setting_fields', $this->setting_fields, $this );
	}

	public function get_content_type() {
		switch ( '' ) {
			case 'html' :
				return 'text/html';
			case 'multipart' :
				return 'multipart/alternative';
			default :
				return 'text/plain';
		}
	}

	/**
	 * Determines if this email template enable for using.
	 *
	 * @return boolean
	 */
	public function is_enabled() {
		return 'on' === abrs_sanitize_checkbox( $this->enabled );
	}

	public function is_manually() {
	}

	/**
	 * Checks if this email is customer focussed.
	 * @return bool
	 */
	public function is_customer_email() {
		return;
	}

	/**
	 * Get the recipients.
	 *
	 * @return string
	 */
	public function get_recipient() {
		return abrs_sanitize_email(
			apply_filters( "awebooking/email/recipient_{$this->id}", $this->recipient, $this )
		);
	}

	/**
	 * Get email subject.
	 *
	 * @return string
	 */
	public function get_subject() {
	}

	/**
	 * Get mail message.
	 *
	 * @return string
	 */
	public function get_body() {
		$mailer    = $this;
		$content   = $mailer->build();

		$content   = abrs_get_template_content( 'emails/layouts/' . $this->layout, compact( 'content', 'mailer' ) );
		$styleshee = abrs_get_template_content( sprintf( 'emails/themes/%s.css', rtrim( $this->style, '.css' ) ) );

		// Apply CSS styles inline for picky email clients.
		$emogrifier = new Emogrifier( $content, $styleshee );

		try {
			$content = $emogrifier->emogrify();
		} catch ( \Exception $e ) {
			$content = '';
		}

		return $content;
	}

	/**
	 * Build the message.
	 *
	 * @return string
	 */
	abstract protected function build();

	/**
	 * Initialise settings fields.
	 *
	 * @return void
	 */
	protected function setup_fields() {
		$this->setting_fields = [
			'enable'          => [
				'name'        => esc_html__( 'Enable / Disable', 'awebooking' ),
				'type'        => 'toggle',
				'label'       => esc_html__( 'Enable this email notification', 'awebooking' ),
				'default'     => 'on',
			],
			'email_type'      => [
				'name'        => esc_html__( 'Email Type', 'awebooking' ),
				'type'        => 'select',
				'description' => esc_html__( 'Choose which format of email to send.', 'awebooking' ),
				'default'     => 'html',
				// 'options'     => $this->get_email_type_options(),
				'tooltip'     => true,
			],
			'subject'         => [
				'name'        => esc_html__( 'Subject', 'awebooking' ),
				'type'        => 'text',
				'tooltip'     => true,
				/* translators: %s: list of placeholders */
				'description' => sprintf( __( 'Available placeholders: %s', 'awebooking' ), '<code>' . implode( '</code>, <code>', array_keys( $this->placeholders ) ) . '</code>' ),
				// 'default'     => $this->get_default_subject(),
			],
			'heading'         => [
				'name'        => esc_html__( 'Email heading', 'awebooking' ),
				'type'        => 'text',
				'tooltip'     => true,
				/* translators: %s: list of placeholders */
				'description' => sprintf( __( 'Available placeholders: %s', 'awebooking' ), '<code>' . implode( '</code>, <code>', array_keys( $this->placeholders ) ) . '</code>' ),
				// 'default'     => $this->get_default_heading(),
			],
			'content'         => [
				'name'        => esc_html__( 'Email content', 'awebooking' ),
				'id'          => 'email_cancelled_content',
				'type'        => 'wysiwyg',
				// 'default'     => awebooking( 'setting' )->get_default( 'email_cancelled_content' ),
				// 'after'       => $this->get_shortcodes_notes(),
				'options'     => [
					'tinymce'       => false,
					'media_buttons' => false,
				],
			],
		];
	}

	/**
	 * Email type options.
	 *
	 * @return array
	 */
	public function get_email_type_options() {
		$types = array( 'plain' => esc_html__( 'Plain text', 'awebooking' ) );

		if ( class_exists( 'DOMDocument' ) ) {
			$types['html']      = esc_html__( 'HTML', 'awebooking' );
		}

		return $types;
	}

	/**
	 * Format a given string.
	 *
	 * @param  mixed $string Input string to format.
	 * @return string
	 */
	public function format_string( $string ) {
		$placeholders = apply_filters( 'awebooking/mail/placeholders', $this->get_placeholders(), $this );

		return str_replace( array_keys( $placeholders ), array_keys( $placeholders ), $string );
	}

	/**
	 * Set the placeholders.
	 *
	 * @return array
	 */
	public function get_placeholders() {
		return wp_parse_args( $this->placeholders, [
			'{blogname}'   => $this->get_blogname(),
			'{site_title}' => $this->get_blogname(),
		]);
	}

	/**
	 * Get blog name formatted for emails.
	 *
	 * @return string
	 */
	public function get_blogname() {
		return wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES );
	}

	/**
	 * Load a partial template.
	 *
	 * @param  string $template Template name.
	 * @param  array  $args     Send variables to template.
	 * @return void
	 */
	public function template( $template, $args = [] ) {
		$template = sprintf( 'emails/%s.php', rtrim( $template, '.php' ) );

		// Pass this object as 'mailer' instance.
		$args['mailer'] = $this;

		abrs_get_template( $template, $args );
	}

	/**
	 * Load a partial template.
	 *
	 * @param  string $partial Partial template name (emails/partials).
	 * @param  array  $args    Send variables to partial template.
	 * @return void
	 */
	public function partial( $partial, array $args = [] ) {
		$template = sprintf( 'emails/partials/%s.php', rtrim( $partial, '.php' ) );

		// Pass this object as 'mailer' instance.
		$args['mailer'] = $this;

		abrs_get_template( $template, $args );
	}
}
