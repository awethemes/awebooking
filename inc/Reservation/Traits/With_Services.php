<?php
namespace AweBooking\Reservation\Traits;

use WP_Error;
use AweBooking\Model\Service;
use AweBooking\Reservation\Item;

trait With_Services {
	/**
	 * List the booked services.
	 *
	 * @var \AweBooking\Support\Collection
	 */
	protected $services;

	/**
	 * Gets all services.
	 *
	 * @return \AweBooking\Support\Collection
	 */
	public function get_services() {
		return $this->services;
	}

	/**
	 * Add a services to the reservation.
	 *
	 * @param  Service|int $service  The service ID.
	 * @param  int         $quantity The quantity.
	 * @param  array       $options  Optional, the options data.
	 * @return \WP_Error
	 */
	public function add_service( $service, $quantity = 1, $options = [] ) {
		$service = abrs_get_service( $service );

		if ( $quantity <= 0 || ! $service || 'trash' === $service->get( 'status' ) ) {
			return new WP_Error( 'error', esc_html__( 'Invalid service ID.', 'awebooking' ) );
		}

		if ( ! $service->is_purchasable() ) {
			return new WP_Error( 'error', esc_html__( 'Sorry, this service cannot be purchased.', 'awebooking' ) );
		}

		if ( ! $service->is_in_stock() ) {
			/* translators: %s: service name */
			return new WP_Error( 'error', sprintf( __( 'You cannot add &quot;%s&quot; to the reservation because the service is out of stock.', 'awebooking' ), $service->get_name() ) );
		}

		if ( ! $service->has_enough_stock( $quantity ) ) {
			/* translators: 1: service name 2: quantity in stock */
			return new WP_Error( 'error', sprintf( __( 'You cannot add that amount of &quot;%1$s&quot; to the reservation because there is not enough stock (%2$s remaining).', 'awebooking' ), $service->get_name(), wc_format_stock_quantity_for_display( $service->get_stock_quantity(), $service ) ) );
		}
	}

	public function remove_service( $service ) {
	}

	/**
	 * Restore the services from session.
	 *
	 * @return void
	 */
	public function restore_services() {
		$this->sets_included_services();
	}

	/**
	 * Perform sets included services.
	 *
	 * @return void
	 */
	protected function sets_included_services() {
		$this->services = collect();

		foreach ( $this->get_included_services() as $service ) {
			$item = new Item( [
				'name'             => $service->get( 'name' ),
				'price'            => 0, // alway zero.
				'quantity'         => 1,
				'data'             => $service,
				'associated_model' => get_class( $service ),
				'options'          => [ 'included' => true ],
			]);

			$this->services->put( $item->get_row_id(), $item );
		}

		do_action( 'abrs_added_included_services', $this );
	}

	/**
	 * Gets all includes services have in room stays.
	 *
	 * These services come with the room rate, no extra cost charge.
	 *
	 * @return \AweBooking\Support\Collection
	 */
	public function get_included_services() {
		// @see https://github.com/kalessil/phpinspectionsea/blob/master/docs/performance.md#slow-array-function-used-in-loop
		$services = [ [] ];

		foreach ( $this->get_room_stays() as $room_stay ) {
			/* @var \AweBooking\Model\Pricing\Contracts\Rate $rate */
			$rate = $room_stay->data()->get_rate_plan();

			$services[] = (array) $rate->get_services();
		}

		// Merges all items.
		$services = wp_parse_id_list( array_merge( ...$services ) );

		return abrs_collect( $services )
			->map_into( Service::class )
			->keyBy( 'id' );
	}
}
