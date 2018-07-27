<?php
namespace AweBooking\Frontend\Shortcodes;

class Search_Form_Shortcode extends Shortcode_Abstract {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->defaults = abrs_search_form_default_atts();
	}

	/**
	 * Default attributes.
	 *
	 * @var array
	 */
	protected $defaults = [];

	/**
	 * {@inheritdoc}
	 */
	public function output( $request ) {
		abrs_get_search_form( $this->atts );
	}
}
