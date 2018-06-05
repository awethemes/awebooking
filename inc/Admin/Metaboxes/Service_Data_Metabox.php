<?php
namespace AweBooking\Admin\Metaboxes;

use Awethemes\Http\Request;
use AweBooking\Admin\Forms\Term_Service_Form;

class Service_Data_Metabox {
	/**
	 * Constructor.
	 */
	public function __construct() {
	}

	/**
	 * Output the metabox.
	 *
	 * @param \WP_Term $term The WP_Term object.
	 */
	public function output( $term ) {
		( new Term_Service_Form( $term ) )->show_form();
	}

	/**
	 * Handle save the the metabox.
	 *
	 * @param \WP_Post                $post    The WP_Post object instance.
	 * @param \Awethemes\Http\Request $request The HTTP Request.
	 */
	public function save( $post, Request $request ) {
	}
}
