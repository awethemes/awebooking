<?php
namespace AweBooking\Email;

class Mailer {
	/**
	 * Email addresses to send message.
	 *
	 * @var string|array
	 */
	public $to;

	/**
	 * Email subject.
	 *
	 * @var string
	 */
	public $subject;

	/**
	 * Additional headers.
	 *
	 * @var string
	 */
	public $headers;

	/**
	 * Files to attach.
	 *
	 * @var string|array
	 */
	public $attachments;

	/**
	 * Send HTML emails.
	 *
	 * @param  tring|array $to      Email addresses to send message.
	 * @param  string      $subject Email subject.
	 * @return static
	 */
	public static function to( $to, $subject = null ) {
		return new static( $to, $subject );
	}

	/**
	 * Send HTML emails from AweBooking using wp_mail().
	 *
	 * @param string|array $to          Array or comma-separated list of email addresses to send message.
	 * @param string       $subject     Email subject.
	 * @param string|array $headers     Optional. Additional headers.
	 * @param string|array $attachments Optional. Files to attach.
	 */
	public function __construct( $to, $subject = null, $headers = "Content-Type: text/html\r\n", $attachments = '' ) {
		$this->to = $to;
		$this->subject = $subject;
		$this->headers = $headers;
		$this->attachments = $attachments;
	}

	/**
	 * Set email subject.
	 *
	 * @param  string $subject Email subject.
	 * @return $this
	 */
	public function subject( $subject ) {
		$this->subject = $subject;

		return $this;
	}

	/**
	 * Set email headers.
	 *
	 * @param  string|array $headers Email headers.
	 * @return $this
	 */
	public function headers( $headers ) {
		$this->headers = $headers;

		return $this;
	}

	/**
	 * Set email attachments.
	 *
	 * @param  string|array $attachments Email attachments format.
	 * @return $this
	 */
	public function attachments( $attachments ) {
		$this->attachments = $attachments;

		return $this;
	}

	/**
	 * Send mailable template.
	 *
	 * @param  Mailable $mail Mailable object instance.
	 * @return bool
	 */
	public function send( Mailable $mail ) {
		add_filter( 'wp_mail_from', [ $this, 'get_from_address' ] );
		add_filter( 'wp_mail_from_name', [ $this, 'get_from_name' ] );

		$subject = $this->subject ? $this->subject : $mail->get_subject();
		$return = wp_mail( $this->to, $subject, $mail->get_body(), $this->headers, $this->attachments );

		remove_filter( 'wp_mail_from', [ $this, 'get_from_address' ] );
		remove_filter( 'wp_mail_from_name', [ $this, 'get_from_name' ] );

		return $return;
	}

	/**
	 * Get the from name for outgoing emails.
	 *
	 * @param  string $from_name Name associated with the "from" email address.
	 * @return string
	 */
	public function get_from_name( $from_name ) {
		$name = apply_filters( 'awebooking/email_from_name', abrs_get_option( 'email_from_name' ), $this );

		return $name ? wp_specialchars_decode( esc_html( $name ), ENT_QUOTES ) : $from_name;
	}

	/**
	 * Get the from address for outgoing emails.
	 *
	 * @param  string $from_email Email address to send from.
	 * @return string
	 */
	public function get_from_address( $from_email ) {
		$from_address = apply_filters( 'awebooking/email_from_address', abrs_get_option( 'email_from_address' ), $this );

		return $from_address ? sanitize_email( $from_address ) : $from_email;
	}
}
