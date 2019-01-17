<?php

namespace AweBooking\Email;

class Message {
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
	 * Email content.
	 *
	 * @var string
	 */
	public $content;

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
	 * Send HTML emails from AweBooking using wp_mail().
	 *
	 * @param string|array $to          Array or comma-separated list of email addresses to send message.
	 * @param string       $subject     Email subject.
	 * @param string       $content     Email content.
	 * @param string|array $headers     Optional. Additional headers.
	 * @param string|array $attachments Optional. Files to attach.
	 */
	public function __construct( $to, $subject = null, $content = '', $headers = "Content-Type: text/html\r\n", $attachments = [] ) {
		$this->to          = $to;
		$this->subject     = $subject;
		$this->content     = $content;
		$this->headers     = $headers;
		$this->attachments = $attachments;
	}

	/**
	 * Sets the email content.
	 *
	 * @param  string $content Email content.
	 * @return $this
	 */
	public function content( $content ) {
		$this->content = $content;

		return $this;
	}

	/**
	 * Sets the email subject.
	 *
	 * @param  string $subject Email subject.
	 * @return $this
	 */
	public function subject( $subject ) {
		$this->subject = $subject;

		return $this;
	}

	/**
	 * Sets the email headers.
	 *
	 * @param  string|array $headers Email headers.
	 * @return $this
	 */
	public function headers( $headers ) {
		$this->headers = $headers;

		return $this;
	}

	/**
	 * Sets the email attachments.
	 *
	 * @param  string|array $attachments Email attachments.
	 * @return $this
	 */
	public function attachments( $attachments ) {
		$this->attachments = $attachments;

		return $this;
	}

	/**
	 * Send the message.
	 *
	 * @return bool
	 */
	public function send() {
		$to = is_string( $this->to ) ? array_map( 'trim', explode( ',', $this->to ) ) : $this->to;
		$to = array_unique( array_filter( $to, 'is_email' ) );

		if ( empty( $to ) ) {
			return false;
		}

		add_filter( 'wp_mail_from', [ $this, 'get_from_address' ] );
		add_filter( 'wp_mail_from_name', [ $this, 'get_from_name' ] );

		$sended = abrs_rescue( function () use ( $to ) {
			return wp_mail( $to, $this->subject, $this->content, $this->headers, $this->attachments );
		}, false );

		remove_filter( 'wp_mail_from', [ $this, 'get_from_address' ] );
		remove_filter( 'wp_mail_from_name', [ $this, 'get_from_name' ] );

		return $sended;
	}

	/**
	 * Get the "from name" for outgoing emails.
	 *
	 * @param  string $from_name Name associated with the "from" email address.
	 * @return string
	 */
	public function get_from_name( $from_name ) {
		$name = apply_filters( 'abrs_email_from_name', abrs_get_option( 'email_from_name' ), $this );

		return $name ? wp_specialchars_decode( esc_html( $name ), ENT_QUOTES ) : $from_name;
	}

	/**
	 * Get the "from address" for outgoing emails.
	 *
	 * @param  string $from_email Email address to send from.
	 * @return string
	 */
	public function get_from_address( $from_email ) {
		$from_address = apply_filters( 'abrs_email_from_address', abrs_get_option( 'email_from_address' ), $this );

		return $from_address ? sanitize_email( $from_address ) : $from_email;
	}
}
