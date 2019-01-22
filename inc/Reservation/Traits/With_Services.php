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
	 * Gets all includes services have in room stays.
	 *
	 * These services come with the room rate, no extra cost charge.
	 *
	 * @return array
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
		return wp_parse_id_list( array_merge( ...$services ) );
	}

	/**
	 * Gets all service items.
	 *
	 * @param bool $exclude_included Exclude includes service.
	 *
	 * @return \AweBooking\Support\Collection
	 */
	public function get_services( $exclude_included = false ) {
		if ( ! $exclude_included ) {
			return $this->services;
		}

		return $this->services->whereNotIn( 'id', $this->get_included_services() );
	}

	/**
	 * Gets a service item by given ID.
	 *
	 * @param  string $service_id The service ID.
	 * @return \AweBooking\Reservation\Item
	 */
	public function get_service( $service_id ) {
		if ( is_string( $service_id ) && $this->services->has( $service_id ) ) {
			return $this->services->get( $service_id );
		}

		return $this->services
			->where( 'id', '=', $this->parse_service( $service_id ) )
			->first();
	}

	/**
	 * Determine if a service item exists in the reservation.
	 *
	 * @param  string $service_id The service ID.
	 * @return bool
	 */
	public function has_service( $service_id ) {
		if ( is_string( $service_id ) && $this->services->has( $service_id ) ) {
			return true;
		}

		return $this->services
			->where( 'id', '=', $this->parse_service( $service_id ) )
			->isNotEmpty();
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

		$selectable = true;

		// Force the quanity to 1 in non-selectable.
		if ( ! $service->is_quantity_selectable() ) {
			$quantity   = 1;
			$selectable = false;
		}

		$row_id = Item::generate_row_id( $service->get_id(), $options );

		if ( $selectable ) {
			if ( ! $service->is_in_stock() ) {
				/* translators: %s: service name */
				return new WP_Error( 'error', sprintf( __( 'You cannot add &quot;%s&quot; to the reservation because the service is out of stock.', 'awebooking' ), $service->get_name() ) );
			}

			if ( ! $service->has_enough_stock( $quantity ) ) {
				/* translators: 1: service name 2: quantity in stock */
				return new WP_Error( 'error', sprintf( __( 'You cannot add that amount of &quot;%1$s&quot; to the reservation because there is not enough stock (%2$s remaining).', 'awebooking' ), $service->get( 'name' ), number_format_i18n( $service->get( 'stock_quantity' ) ) ) );
			}
		}

		if ( $this->services->has( $row_id ) ) {
			$item = $this->services->get( $row_id );
			$item->set( 'quantity', $selectable ? $item->get( 'quantity' ) + $quantity : 1 );
		} else {
			$item = new Item([
				'id'               => $service->get_id(),
				'name'             => $service->get( 'name' ),
				'price'            => 0, // Sets the price late.
				'quantity'         => $quantity,
				'data'             => $service,
				'associated_model' => Service::class,
				'options'          => $options,
			]);

			$this->services->put( $item->get_row_id(), $item );
		}
	}

	/**
	 * Remove a service item.
	 *
	 * @param  string $service The service ID.
	 * @return void
	 */
	public function remove_service( $service ) {
		$item = $this->get_service( $service );

		if ( $item && ! in_array( $item->get_id(), $this->get_included_services() ) ) {
			$this->services->forget( $item->get_row_id() );
		}
	}

	/**
	 * Parse the service ID.
	 *
	 * @param Service|int $service The service ID or instance.
	 * @return int
	 */
	protected function parse_service( $service ) {
		return $service instanceof Service ? $service->get_id() : (int) $service;
	}

	// TODO:
	protected function restore_services() {
		$this->sets_included_services();

		$session_services = $this->store->get( 'booked_services' );

		if ( empty( $session_services ) || ! is_array( $session_services ) ) {
			return;
		}

		foreach ( $session_services as $row_id => $data_service ) {
			$this->services[ $row_id ] = ( new Item )->update( $data_service );
		}
	}

	/**
	 * Perform sets included services.
	 *
	 * @return void
	 */
	protected function sets_included_services() {
		$this->services = abrs_collect();

		foreach ( $this->get_included_services() as $service ) {
			$service = abrs_get_service( $service );

			if ( ! $service ) {
				continue;
			}

			$item = new Item( [
				'id'               => $service->get_id(),
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
}
