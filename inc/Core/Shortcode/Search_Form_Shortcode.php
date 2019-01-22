<?php

namespace AweBooking\Core\Shortcode;

class Search_Form_Shortcode extends Shortcode {
	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->defaults = abrs_search_form_default_atts();
	}

	/**
	 * {@inheritdoc}
	 */
	public function output( $request ) {
		abrs_get_search_form( $this->atts );
	}
}
