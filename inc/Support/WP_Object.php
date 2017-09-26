<?php
namespace AweBooking\Support;

use Awethemes\WP_Object\WP_Object as Base_WP_Object;

abstract class WP_Object extends Base_WP_Object {
	/**
	 * Prefix for hooks.
	 *
	 * @var string
	 */
	protected $prefix = 'awebooking';
}
