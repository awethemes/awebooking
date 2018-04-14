<?php
namespace AweBooking\Frontend\Shortcodes;

class Search_Form_Shortcode extends Shortcode_Abstract {
	/**
	 * Default attributes.
	 *
	 * @var array
	 */
	protected $defaults = [
		'layout'        => '',
		'hide_location' => false,
	];

	/**
	 * {@inheritdoc}
	 */
	public function output( $request ) {
		abrs_get_search_form( $this->atts );
	}
}
