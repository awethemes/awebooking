<?php

namespace AweBooking\Model\Booking;

class Service_Item extends Item {
	/**
	 * Name of object type.
	 *
	 * @var string
	 */
	protected $object_type = 'service_item';

	/**
	 * Name of item type.
	 *
	 * @var string
	 */
	protected $type = 'service_item';

	/**
	 * {@inheritdoc}
	 */
	protected function setup_attributes() {
		$this->attributes = apply_filters( $this->prefix( 'attributes' ), array_merge( $this->attributes, [
			'service_id'   => 0,
			'quantity'     => 1,
			'subtotal'     => 0, // Pre-discount.
			'subtotal_tax' => 0,
			'total'        => 0,
			'total_tax'    => 0,
			'price'        => 0,
		]));
	}

	/**
	 * {@inheritdoc}
	 */
	protected function map_attributes() {
		$this->maps = apply_filters( $this->prefix( 'map_attributes' ), [
			'service_id'   => '_service_id',
			'quantity'     => '_service_quantity',
			'subtotal'     => '_service_subtotal',
			'subtotal_tax' => '_service_subtotal_tax',
			'total'        => '_service_total',
			'total_tax'    => '_service_total_tax',
			'price'        => '_price',
		]);
	}

	/**
	 * {@inheritdoc}
	 */
	protected function sanitize_attribute( $key, $value ) {
		switch ( $key ) {
			case 'quantity':
			case 'service_id':
				$value = absint( $value );
				break;
			case 'total':
			case 'total_tax':
			case 'subtotal':
			case 'subtotal_tax':
			case 'price':
				$value = abrs_sanitize_decimal( $value );
				break;
		}

		return parent::sanitize_attribute( $key, $value );
	}
}
