<?php
namespace AweBooking\Admin\Metaboxes;

use AweBooking\Constants;

class Amenity_Metabox extends Taxonomy_Metabox {
	/**
	 * Post type ID to register meta-boxes.
	 *
	 * @var string
	 */
	protected $taxonomy = Constants::HOTEL_AMENITY;

	/**
	 * Register fields.
	 *
	 * @return void
	 */
	public function register() {
		if ( function_exists( 'wp_simple_iconfonts' ) ) {
			$this->add_field([
				'name'       => esc_html__( 'Icon', 'awebooking' ),
				'id'         => '_icon',
				'type'       => 'simple_iconfonts',
			]);
		}
	}
}
