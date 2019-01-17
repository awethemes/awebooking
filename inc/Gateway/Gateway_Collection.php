<?php

namespace AweBooking\Gateway;

use AweBooking\Support\Collection;

class Gateway_Collection extends Collection {
	/**
	 * Returns the list sorted gateways.
	 *
	 * @return static
	 */
	public function sorted() {
		$ordering = (array) abrs_get_option( 'list_gateway_order', [] );

		// Since we dont have any sorting, return all gateways.
		if ( empty( $ordering ) ) {
			return new static( $this->all() );
		}

		$ordering = abrs_sort_by_keys( array_keys( $this->items ), $ordering );
		$gateways = [];

		foreach ( $ordering as $gateway ) {
			if ( $this->has( $gateway ) ) {
				$gateways[ $gateway ] = $this->get( $gateway );
			}
		}

		return new static( $gateways );
	}

	/**
	 * Returns the gateways enable only.
	 *
	 * @param bool $sorted With sorted gateways.
	 * @return static
	 */
	public function enabled( $sorted = true ) {
		if ( $sorted ) {
			$gateways = $this->sorted();
		} else {
			$gateways = new static( $this );
		}

		return $gateways->filter( function( $gateway ) {
			/* @var $gateway \AweBooking\Gateway\Gateway */
			return $gateway->is_enabled();
		});
	}
}
