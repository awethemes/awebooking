<?php
namespace AweBooking\Mail;

use AweBooking\Interfaces\Mailable as Mailable_Interface;

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
	 * @param  Mailable_Interface $mail Mailable object instance.
	 * @return boolean
	 */
	public function send( Mailable_Interface $mail ) {
		add_filter( 'wp_mail_from', array( $this, 'get_from_address' ) );
		add_filter( 'wp_mail_from_name', array( $this, 'get_from_name' ) );

		$return = wp_mail( $this->to, $this->subject, $mail->message(), $this->headers, $this->attachments );

		remove_filter( 'wp_mail_from', array( $this, 'get_from_address' ) );
		remove_filter( 'wp_mail_from_name', array( $this, 'get_from_name' ) );

		return $return;
	}

	/**
	 * Get the from name for outgoing emails.
	 *
	 * @return string
	 */
	public function get_from_name() {
		$from_name = apply_filters( 'awebooking/email_from_name', abkng_config( 'email_from_name' ), $this );

		return wp_specialchars_decode( esc_html( $from_name ), ENT_QUOTES );
	}

	/**
	 * Get the from address for outgoing emails.
	 *
	 * @return string
	 */
	public function get_from_address() {
		$from_address = apply_filters( 'awebooking/email_from_address', abkng_config( 'email_from_address' ), $this );

		return sanitize_email( $from_address );
	}
}
