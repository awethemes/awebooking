<?php
namespace AweBooking\Model;

use AweBooking\Constants;
use AweBooking\Model\WP_Object;

class Amenity extends WP_Object {
	/**
	 * This is the name of this object type.
	 *
	 * @var string
	 */
	protected $object_type = Constants::HOTEL_AMENITY;

	/**
	 * WordPress type for object, Eg: "post" and "term".
	 *
	 * @var string
	 */
	protected $wp_type = 'term';

	/**
	 * Type of object metadata is for (e.g., term, post).
	 *
	 * @var string
	 */
	protected $meta_type = 'term';

	/**
	 * Setup the object attributes.
	 *
	 * @return void
	 */
	protected function setup() {
	}
}
