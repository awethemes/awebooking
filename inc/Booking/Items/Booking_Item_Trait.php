<?php
namespace AweBooking\Booking\Items;

use AweBooking\Factory;

trait Booking_Item_Trait {
	/**
	 * Booking items will be stored here.
	 *
	 * @var Collection
	 */
	protected $items;

	/**
	 * Booking items that need deleting are stored here.
	 *
	 * @var array
	 */
	protected $items_to_delete = [];

	/**
	 * Adds a booking item to this booking.
	 *
	 * The booking item will not persist until save.
	 *
	 * @param  Booking_Item $item Booking item instance.
	 * @return bool
	 */
	public function add_item( Booking_Item $item ) {
		$item_type = $item->get_type();

		// A line item must be have type name, so if someone
		// given invalid line item, we just leave and do nothing.
		if ( empty( $item_type ) ) {
			return false;
		}

		// Prevent add a already exists item.
		if ( $item->exists() && $this->has_item( $item->get_id() ) ) {
			return false;
		}

		// Set the booking ID for item.
		if ( $this->exists() ) {
			$item->set_booking_id( $this->get_id() );
		}

		$this->items->push( $item );

		return true;
	}

	/**
	 * Remove item from the booking.
	 *
	 * @param  int $item_id Booking item ID.
	 * @return boolean
	 */
	public function remove_item( $item_id ) {
		$item = Factory::resolve_booking_item( $item_id );

		if ( ! $item || ! $this->has_item( $item_id ) ) {
			return false;
		}

		// Unset and remove later.
		foreach ( $this->items->all() as $index => $_item ) {
			if ( $item->get_id() === $_item->get_id() ) {
				$this->items->forget( $index );
				break;
			}
		}

		$this->items_to_delete[] = $item;

		return true;
	}

	/**
	 * Determines an item ID have in items.
	 *
	 * @param  int $item_id Booking item ID.
	 * @return boolean
	 */
	public function has_item( $item_id ) {
		return ! is_null( $this->get_item( $item_id ) );
	}

	/**
	 * Returns item instance by ID.
	 *
	 * @param  Booking_Item|int $item_id Booking item ID.
	 * @return Booking_Item|null
	 */
	public function get_item( $item_id ) {
		$item_id = ( $item_id instanceof Booking_Item ) ? $item_id->get_id() : (int) $item_id;

		foreach ( $this->items->all() as $key => $item ) {
			if ( $item->get_id() === $item_id ) {
				return $item;
			}
		}
	}

	/**
	 * Get items by type.
	 *
	 * @param  string $type Item type.
	 * @return Collection
	 */
	public function get_items( $type ) {
		return $this->items->filter(function( $item ) {
			return $item->get_type() === $type;
		});
	}

	/**
	 * Save all boooking items which are part of this boooking.
	 */
	protected function save_items() {
		foreach ( $this->items_to_delete as $delete_item ) {
			$delete_item->delete();
		}

		foreach ( $this->items->all() as $item ) {
			$item->set_booking_id( $this->get_id() );
			$item->save();
		}

		$this->items_to_delete = [];
	}

	/**
	 * Returns collection of line items.
	 *
	 * @return Collection
	 */
	public function get_line_items() {
		return $this->get_items( 'line_item' );
	}

	/**
	 * Get and setup booking items from database.
	 *
	 * @return void
	 */
	protected function setup_booking_items() {
		if ( ! $this->exists() ) {
			return;
		}

		// Try get in the cache first.
		$items = wp_cache_get( $this->get_id(), 'awebooking_cache_booking_items' );

		if ( false === $items ) {
			global $wpdb;

			$items = $wpdb->get_results(
				$wpdb->prepare( "SELECT * FROM `{$wpdb->prefix}awebooking_booking_items` WHERE `booking_id` = %d ORDER BY `booking_item_id`", $this->get_id() ),
				ARRAY_A
			);

			// Cache each single item.
			foreach ( $items as &$item ) {
				// Santize before cache current booking-item.
				$item['booking_id'] = (int) $item['booking_id'];
				$item['booking_item_id'] = (int) $item['booking_item_id'];

				wp_cache_add( $item['booking_item_id'], $item, 'awebooking_cache_booking_item' );
			}

			wp_cache_add( $this->get_id(), $items, 'awebooking_cache_booking_items' );
		}

		// TODO: ...
		foreach ( $items as $item ) {
			$booking_item = Factory::resolve_booking_item( $item );
			if ( ! $booking_item ) {
				continue;
			}

			// Maybe have bugs in this.
			$this->add_item( $booking_item );
		}
	}
}
