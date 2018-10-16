<?php
namespace AweBooking\Component\Html;

class Html_String {
	/**
	 * The HTML string.
	 *
	 * @var string
	 */
	protected $html;

	/**
	 * Create a new HTML string instance.
	 *
	 * @param  string $html
	 * @return void
	 */
	public function __construct( $html ) {
		$this->html = $html;
	}

	/**
	 * Get the HTML string.
	 *
	 * @return string
	 */
	public function __toString() {
		return $this->html;
	}
}
