<?php

namespace AweBooking\Model\Booking;

class Tax_Item extends Item {
	/**
	 * Name of item type.
	 *
	 * @var string
	 */
	protected $type = 'tax_item';

	/**
	 * Name of object type.
	 *
	 * @var string
	 */
	protected $object_type = 'tax_item';

	/**
	 * {@inheritdoc}
	 */
	protected function setup_attributes() {
		$this->attributes = apply_filters( $this->prefix( 'attributes' ), array_merge( $this->attributes, [
			'rate_id'       => 0,
			'rate_amount'   => 0,
			'rate_compound' => false,
			'tax_total'     => 0,
		]));
	}

	/**
	 * {@inheritdoc}
	 */
	protected function map_attributes() {
		$this->maps = apply_filters( $this->prefix( 'map_attributes' ), [
			'rate_id'       => '_tax_rate_id',
			'rate_amount'   => '_tax_rate_amount',
			'rate_compound' => '_tax_rate_compound',
			'tax_total'     => '_tax_total',
		]);
	}
}
