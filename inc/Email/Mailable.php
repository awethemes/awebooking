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
	 * Manually send this email.
	 *
	 * @var boolean
	 */
	protected $manually = false;

	/**
	 * Is this is a customer_email.
	 *
	 * @var boolean
	 */
	protected $customer_email = false;

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
	 * Constructor.
	 */
	public function __construct() {
		$this->setup();

		// Default placeholders.
		$this->placeholders = array_merge( $this->placeholders, [
			'{site_title}' => $this->get_blogname(),
		]);

		// Setup fields and settings.
		if ( ! empty( $this->id ) ) {
			$this->setup_fields();

			$this->enabled = $this->get_option( 'enabled' );

			if ( isset( $this->setting_fields['recipient'] ) ) {
				$this->recipient = $this->get_option( 'recipient' );
			}
		}

		$this->init();
	}

	/**
	 * Setup the email template.
	 *
	 * @return void
	 */
	abstract public function setup();

	/**
	 * Init hook to trigger email sent.
	 *
	 * @return void
	 */
	public function init() {}

	/**
	 * Gets template ID.
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
	 * Determines if this email template enable for using.
	 *
	 * @return boolean
	 */
	public function is_enabled() {
		return 'on' === abrs_sanitize_checkbox( $this->enabled );
	}

	/**
	 * Determines if this email is manually sent.
	 *
	 * @return bool
	 */
	public function is_manually() {
		return $this->manually;
	}

	/**
	 * Determines if this email is customer focussed.
	 *
	 * @return bool
	 */
	public function is_customer_email() {
		return $this->customer_email;
	}

	/**
	 * Gets email content type.
	 *
	 * @return string
	 */
	public function get_content_type() {
		return ( 'html' === $this->get_option( 'email_type' ) && class_exists( 'DOMDocument' ) ) ? 'text/html' : 'text/plain';
	}

	/**
	 * Gets the recipients.
	 *
	 * @return string
	 */
	public function get_recipient() {
		return abrs_sanitize_email( apply_filters( "awebooking/email/recipient_{$this->id}", $this->recipient, $this ) );
	}

	/**
	 * Gets email subject.
	 *
	 * @return string
	 */
	public function get_subject() {
		return apply_filters( "awebooking/email/subject_{$this->id}", $this->format_string( $this->get_option( 'subject' ) ), $this );
	}

	/**
	 * Gets email attachments.
	 *
	 * @return array
	 */
	public function get_attachments() {
		return apply_filters( 'awebooking/email/attachments', [], $this );
	}

	/**
	 * Gets email headers.
	 *
	 * @return string
	 */
	public function get_headers() {
		$header = "Content-Type: {$this->get_content_type()}\r\n";

		return apply_filters( 'awebooking/email/headers', $header, $this );
	}

	/**
	 * Get the email content in plain text format.
	 *
	 * @return string
	 */
	public function get_content_plain() {
		return '';
	}

	/**
	 * Get the email content in HTML format.
	 *
	 * @return string
	 */
	public function get_content_html() {
		return '';
	}

	/**
	 * Gets the email content.
	 *
	 * @return string
	 */
	public function get_content() {
		if ( 'text/plain' === $this->get_content_type() ) {
			$body = abrs_esc_plan_text( $this->get_content_plain() );
		} else {
			$body = $this->apply_inline_style( $this->get_content_html() );
		}

		return apply_filters( 'awebooking/email/body', wordwrap( $body, 70 ), $this );
	}

	/**
	 * Apply inline styles to dynamic content.
	 *
	 * @param  string $content The email content.
	 * @return string
	 */
	public function apply_inline_style( $content ) {
		if ( empty( $content ) ) {
			return '';
		}

		// Make sure we only inline CSS for html emails.
		if ( in_array( $this->get_content_type(), [ 'text/html' ] ) && class_exists( 'DOMDocument' ) ) {
			$stylesheet = apply_filters( 'awebooking/email/stylesheets', abrs_get_template_content( 'emails/styles.php' ) );

			// Apply CSS styles inline for picky email clients.
			$emogrifier = new Emogrifier( $content, $stylesheet );

			try {
				$content = $emogrifier->emogrify();
			} catch ( \Exception $e ) {
				abrs_report( $e );
			}
		}

		return $content;
	}

	/**
	 * Build the message.
	 *
	 * @param  mixed $data The data.
	 * @return string
	 */
	public function build( $data ) {
		if ( method_exists( $this, 'prepare_data' ) ) {
			$this->prepare_data( ...func_get_args() );
		}

		return ( new Message( $this->get_recipient() ) )
			->content( $this->get_content() )
			->subject( $this->get_subject() )
			->attachments( $this->get_attachments() )
			->headers( $this->get_headers() );
	}

	/**
	 * Load a components template.
	 *
	 * @param  string $component Conponent name (in emails/components).
	 * @param  string $slot      Conponent slot.
	 * @param  array  $vars      Send variables to component.
	 * @return void
	 */
	public function component( $component, $slot = '', $vars = [] ) {
		$template = sprintf( 'emails/components/%s.php', rtrim( str_replace( '.', '/', $component ), '.php' ) );

		if ( is_array( $slot ) && empty( $vars ) ) {
			$vars = $slot;
		}

		$vars['mail'] = $this;
		$vars['slot'] = is_string( $slot ) ? $slot : '';

		abrs_get_template( $template, $vars );
	}

	/**
	 * Gets the setting fields.
	 *
	 * @return array|null
	 */
	public function get_setting_fields() {
		return apply_filters( 'awebooking/email/setting_fields', $this->setting_fields, $this );
	}

	/**
	 * Initialise settings fields.
	 *
	 * @return void
	 */
	protected function setup_fields() {
		if ( ! $this->is_manually() ) {
			$this->setting_fields['enabled'] = [
				'type'        => 'toggle',
				'name'        => esc_html__( 'Enable / Disable', 'awebooking' ),
				'label'       => esc_html__( 'Enable this email notification', 'awebooking' ),
				'default'     => 'on',
			];
		}

		$this->setting_fields['email_type'] = [
			'type'        => 'select',
			'name'        => esc_html__( 'Type', 'awebooking' ),
			'description' => esc_html__( 'Choose which format of email to send.', 'awebooking' ),
			'options'     => $this->get_email_type_options(),
			'default'     => 'html',
			'tooltip'     => true,
		];

		if ( ! $this->is_customer_email() ) {
			$this->setting_fields['recipient'] = [
				'type'            => 'text',
				'name'            => esc_html__( 'Recipient(s)', 'awebooking' ),
				/* translators: %s: Default admin email */
				'description'     => sprintf( __( 'Enter recipients (comma separated) for this email. Defaults to %s', 'awebooking' ), '<code>' . esc_attr( get_option( 'admin_email' ) ) . '</code>' ),
				'default'         => esc_attr( get_option( 'admin_email' ) ),
				'tooltip'         => true,
				'sanitization_cb' => 'abrs_sanitize_email',
			];
		}

		$this->setting_fields['subject'] = [
			'type'        => 'text',
			'name'        => esc_html__( 'Subject', 'awebooking' ),
			/* translators: %s: list of placeholders */
			'description' => sprintf( __( 'Available placeholders: %s', 'awebooking' ), '<code>' . implode( '</code>, <code>', array_keys( $this->get_placeholders() ) ) . '</code>' ),
			'default'     => $this->get_default_subject(),
		];

		$this->setting_fields['content'] = [
			'type'        => 'wysiwyg',
			'name'        => esc_html__( 'Email content', 'awebooking' ),
			'default'     => $this->get_default_content(),
			'after'       => $this->get_shortcodes_notes(),
			'options'     => [
				'tinymce'       => false,
				'media_buttons' => false,
			],
		];
	}

	/**
	 * Gets default email subject.
	 *
	 * @return string
	 */
	public function get_default_subject() {
		return '';
	}

	/**
	 * Gets default email heading.
	 *
	 * @return string
	 */
	public function get_default_heading() {
		return '';
	}

	/**
	 * Gets default email content.
	 *
	 * @return string
	 */
	public function get_default_content() {
		return '';
	}

	/**
	 * Email type options.
	 *
	 * @return array
	 */
	public function get_email_type_options() {
		$types = [ 'plain' => esc_html__( 'Plain text', 'awebooking' ) ];

		if ( class_exists( 'DOMDocument' ) ) {
			$types['html'] = esc_html__( 'HTML', 'awebooking' );
		}

		return $types;
	}

	/**
	 * Gets the placeholders.
	 *
	 * @return array
	 */
	public function get_placeholders() {
		return apply_filters( 'awebooking/mail/placeholders', $this->placeholders, $this );
	}

	/**
	 * Format a given string.
	 *
	 * @param  mixed $string Input string to format.
	 * @return string
	 */
	public function format_string( $string ) {
		$placeholders = $this->get_placeholders();

		return str_replace( array_keys( $placeholders ), array_values( $placeholders ), $string );
	}

	/**
	 * Gets the option by key.
	 *
	 * @param  string $key     The key.
	 * @param  mixed  $default The default value.
	 * @return mixed
	 */
	public function get_option( $key, $default = null ) {
		if ( is_null( $default ) && isset( $this->setting_fields[ $key ]['default'] ) ) {
			$default = $this->setting_fields[ $key ]['default'];
		}

		return abrs_get_option( "email_{$this->id}_{$key}", $default );
	}

	/**
	 * Gets blog name formatted for emails.
	 *
	 * @return string
	 */
	public function get_blogname() {
		return wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES );
	}

	/**
	 * Get the shortcodes notes.
	 *
	 * @return string
	 */
	protected function get_shortcodes_notes() {
		$shortcodes = [
			'{booking_id}'              => esc_html__( 'The booking ID', 'awebooking' ),
			'{contents}'                => esc_html__( 'The booking contents', 'awebooking' ),
			'{customer_details}'        => esc_html__( 'The customer details', 'awebooking' ),
			'{status}'                  => esc_html__( 'The booking status', 'awebooking' ),
			'{source}'                  => esc_html__( 'The booking source', 'awebooking' ),
			'{created_via}'             => esc_html__( 'The booking created via', 'awebooking' ),
			'{date_created}'            => esc_html__( 'The created date', 'awebooking' ),
			'{date_modified}'           => esc_html__( 'The modified date', 'awebooking' ),
			'{arrival_time}'            => esc_html__( 'The arrival time', 'awebooking' ),
			'{customer_note}'           => esc_html__( 'The customer note', 'awebooking' ),
			'{check_in_date}'           => esc_html__( 'The booking check-in date', 'awebooking' ),
			'{check_out_date}'          => esc_html__( 'The booking check-out date', 'awebooking' ),
			'{discount_tax}'            => esc_html__( 'The discount tax', 'awebooking' ),
			'{total_tax}'               => esc_html__( 'The total tax', 'awebooking' ),
			'{discount_total}'          => esc_html__( 'The discount total', 'awebooking' ),
			'{total}'                   => esc_html__( 'The total amount', 'awebooking' ),
			'{paid}'                    => esc_html__( 'The paid amount', 'awebooking' ),
			'{balance_due}'             => esc_html__( 'The balance due', 'awebooking' ),
			'{customer_id}'             => esc_html__( 'The customer ID', 'awebooking' ),
			'{customer_title}'          => esc_html__( 'The customer title', 'awebooking' ),
			'{customer_first_name}'     => esc_html__( 'The customer first name', 'awebooking' ),
			'{customer_last_name}'      => esc_html__( 'The customer last name', 'awebooking' ),
			'{customer_address}'        => esc_html__( 'The customer address', 'awebooking' ),
			'{customer_address_2}'      => esc_html__( 'The customer address 2', 'awebooking' ),
			'{customer_city}'           => esc_html__( 'The customer city', 'awebooking' ),
			'{customer_state}'          => esc_html__( 'The customer state', 'awebooking' ),
			'{customer_postal_code}'    => esc_html__( 'The customer postal code', 'awebooking' ),
			'{customer_country}'        => esc_html__( 'The customer country', 'awebooking' ),
			'{customer_company}'        => esc_html__( 'The customer company', 'awebooking' ),
			'{customer_phone}'          => esc_html__( 'The customer phone', 'awebooking' ),
			'{customer_email}'          => esc_html__( 'The customer email', 'awebooking' ),
		];

		$contents = '<div class="awebooking-sweet-alert" style="margin-top: 1em; max-width: 100%;">';
		$contents .= '<h4>' . esc_html__( 'You can use following shortcodes in content of this template', 'awebooking' ) . '</h4>';

		$contents .= '<table class="awebooking-minimal-table"><tbody>';
		foreach ( $shortcodes as $key => $value ) {
			$contents .= '<tr><td><code>' . esc_html( $key ) . '</code></td><td>' . esc_html( $value ) . '</td></tr>';
		}

		$contents = apply_filters( 'awebooking/after_add_email_notes', $contents );

		$contents .= '</tbody></table>';
		$contents .= '</div>';

		return $contents;
	}
}
