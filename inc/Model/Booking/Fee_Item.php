<?php

namespace AweBooking\Model\Booking;

class Fee_Item extends Item {
	/**
	 * Name of object type.
	 *
	 * @var string
	 */
	protected $object_type = 'fee_item';

	/**
	 * Name of item type.
	 *
	 * @var string
	 */
	protected $type = 'fee_item';

	/**
	 * {@inheritdoc}
	 */
	protected function setup_attributes() {
		$this->attributes = apply_filters( $this->prefix( 'attributes' ), array_merge( $this->attributes, [
			'amount'    => 0,
			'total'     => 0,
			'total_tax' => 0,
		]));
	}

	/**
	 * {@inheritdoc}
	 */
	protected function map_attributes() {
		$this->maps = apply_filters( $this->prefix( 'map_attributes' ), [
			'amount'    => '_fee_amount',
			'total'     => '_fee_total',
			'total_tax' => '_fee_total_tax',
		]);
	}

	/**
	 * {@inheritdoc}
	 */
	protected function sanitize_attribute( $key, $value ) {
		switch ( $key ) {
			case 'total':
			case 'amount':
			case 'total_tax':
				$value = abrs_sanitize_decimal( $value );
				break;
		}

		return parent::sanitize_attribute( $key, $value );
	}
}
