<?php
namespace AweBooking\Reservation\Traits;

use WP_Error;
use AweBooking\Reservation\Item;

trait With_Fees {
	/**
	 * List the fees.
	 *
	 * @var \AweBooking\Support\Collection
	 */
	protected $fees;

	/**
	 * Add a fee. Fee IDs must be unique.
	 *
	 * @param array $args Array of fee properties.
	 * @return \AweBooking\Reservation\Item|WP_Error
	 */
	public function add_fee( $args = [] ) {
		$args = wp_parse_args( $args, [
			'id'     => '',
			'name'   => '',
			'amount' => 0,
		] );

		$fee = new Item( [
			'id'    => $args['id'],
			'name'  => $args['name'] ?: esc_html__( 'Fee', 'awebooking' ),
			'price' => abrs_sanitize_decimal( $args['amount'] ),
		] );

		$row_id = $fee->get_row_id();

		if ( $this->fees->has( $row_id ) ) {
			return new WP_Error( 'fee_exists', esc_html__( 'Fee has already been added.', 'awebooking' ) );
		}

		// Put the fees into the store.
		$this->fees->put( $row_id, $fee );

		return $this->fees->get( $row_id );
	}

	/**
	 * Get fees sorted by amount.
	 *
	 * @return \AweBooking\Support\Collection
	 */
	public function get_fees() {
		return $this->fees->sortByDesc( 'price' );
	}

	/**
	 * Remove all fees.
	 *
	 * @return void
	 */
	public function remove_fees() {
		$this->fees->clear();
	}
}
