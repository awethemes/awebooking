<?php
namespace AweBooking\Reservation\Pricing;

use AweBooking\Support\Fluent;
use AweBooking\Support\Fluent_Container;

class Context extends Fluent {
	use Fluent_Container;

	/**
	 * An array of defaults attributes.
	 *
	 * @var array
	 */
	protected $defaults = [
		'source'            => '',
		'booking_date'      => null,
		'check_in_date'     => null,
		'check_out_date'    => null,
		'number_adults'     => 0,
		'number_children'   => 0,
		'number_infants'    => 0,
		'overflow_adults'   => 0,
		'overflow_children' => 0,
		'overflow_infants'  => 0,
	];

	/**
	 * Create a new Context instance.
	 *
	 * @param  array|object $attributes The default attributes.
	 * @return void
	 */
	public function __construct( $attributes = [] ) {
		parent::__construct( wp_parse_args( $attributes, $this->defaults ) );
	}
}
