<?php
namespace AweBooking\Mail;

use Pelago\Emogrifier;
use AweBooking\Support\Template;
use AweBooking\Interfaces\Mailable as Mailable_Interface;

abstract class Mailable implements Mailable_Interface {
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
	 * Build the message.
	 *
	 * @return string
	 */
	abstract protected function build();

	/**
	 * Get email subject.
	 *
	 * @return string
	 */
	abstract public function get_subject();

	/**
	 * Get email heading.
	 *
	 * @return string
	 */
	abstract public function get_heading();

	/**
	 * Get mail message.
	 *
	 * @return string
	 */
	public function message() {
		$content = $this->build();
		$content = static::get_template( 'layouts/' . $this->layout, compact( 'content' ) );

		try {
			// Apply CSS styles inline for picky email clients.
			$emogrifier = new Emogrifier( $content, $this->get_stylesheet() );
			$content   = $emogrifier->emogrify();
		} catch ( \Exception $e ) {
			// TODO: Logger error at here.
		}

		return $content;
	}

	/**
	 * Format email string.
	 *
	 * @param mixed $string
	 * @return string
	 */
	public function format_string( $string ) {
		// Find/replace.
		$this->find['blogname']      = '{blogname}';
		$this->find['site-title']    = '{site_title}';
		$this->replace['blogname']   = $this->get_blogname();
		$this->replace['site-title'] = $this->get_blogname();

		return str_replace( apply_filters( 'awebooking/email_format_string_find', $this->find, $this ), apply_filters( 'awebooking/email_format_string_replace', $this->replace, $this ), $string );
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

		Template::get_template( $template, $args );
	}

	/**
	 * Load a partial template.
	 *
	 * @param  string $partial Partial template name (emails/partials/).
	 * @param  array  $args    Send variables to partial template.
	 * @return void
	 */
	public function partial( $partial, array $args = [] ) {
		$template = sprintf( 'emails/partials/%s.php', rtrim( $partial, '.php' ) );

		// Pass this object as 'mailer' instance.
		$args['mailer'] = $this;

		Template::get_template( $template, $args );
	}

	/**
	 * Get a partial template.
	 *
	 * @param  string $template Template name.
	 * @param  array  $args     Send variables to template.
	 * @return string
	 */
	public function get_template( $template, $args = [] ) {
		ob_start();
		$this->template( $template, $args );
		$content = ob_get_clean();

		return $content;
	}

	/**
	 * Get email template stylesheet.
	 *
	 * @return string
	 */
	public function get_stylesheet() {
		$template = sprintf( 'emails/themes/%s.css', rtrim( $this->style, '.css' ) );

		ob_start();
		Template::get_template( $template );
		$css = ob_get_clean();

		return $css;
	}
}
