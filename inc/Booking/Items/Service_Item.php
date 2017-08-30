<?php
namespace AweBooking\Booking\Items;

use AweBooking\Hotel\Service;
use AweBooking\Pricing\Price;
use AweBooking\Booking\Request;
use AweBooking\Booking\Calendar;

class Service_Item extends Booking_Item {
	/**
	 * Name of object type.
	 *
	 * @var string
	 */
	protected $object_type = 'service_item';

	/**
	 * The attributes for this object.
	 *
	 * Name value pairs (name + default value).
	 *
	 * @var array
	 */
	protected $extra_attributes = [
		'service_id'   => '',
		'price'        => 0,
	];

	/**
	 * The attributes that should be cast to native types.
	 *
	 * @var array
	 */
	protected $extra_casts = [
		'service_id' => 'int',
		'price'      => 'float',
	];

	/**
	 * An array of attributes mapped with metadata.
	 *
	 * @var array
	 */
	protected $maps = [
		'price'      => '_line_price',
		'service_id' => '_service_id',
	];

	/**
	 * Returns booking item type.
	 *
	 * @return string
	 */
	public function get_type() {
		return 'service_item';
	}

	/**
	 * Returns service instance.
	 *
	 * @return Service|null
	 */
	public function get_service() {
		if ( ! $this->exists() ) {
			return;
		}

		return new Service( $this['service_id'] );
	}

	public function get_price() {
		return apply_filters( $this->prefix( 'get_price' ), new Price( $this['price'], $this->get_booking()->get_currency() ), $this );
	}
}
