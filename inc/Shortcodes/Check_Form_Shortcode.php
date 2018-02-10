<?php
namespace AweBooking\Shortcodes;

use Awethemes\Http\Request;

class Check_Form_Shortcode extends Shortcode {
	/**
	 * Default shortcode attributes.
	 *
	 * @var array
	 */
	protected $default_atts = [
		'layout'        => '',
		'hide_location' => false,
	];

	/**
	 * {@inheritdoc}
	 */
	public function output( Request $request ) {
		$this->template( 'check-availability-form.php', array( 'atts' => $this->atts ) );
	}
}
