<?php
namespace AweBooking\Booking\Traits;

use AweBooking\Factory;
use AweBooking\Support\Collection;
use AweBooking\Booking\Items\Booking_Item;

trait Booking_Items_Trait {
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
	 * If items did setup or not.
	 *
	 * @var array
	 */
	protected $did_setup_items = false;

	/**
	 * Boot the trait.
	 *
	 * @return void
	 */
	public function boot_booking_items() {
		$this->items = new Collection;
	}

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
		$item = Factory::get_booking_item( $item_id );
		if ( ! $item ) {
			return false;
		}

		// Unset and remove later.
		foreach ( $this->get_all_items() as $index => $_item ) {
			if ( $item->get_id() === $_item->get_id() ) {
				$this->items->forget( $index );
				$this->items_to_delete[] = $_item;

				return true;
			}
		}

		return false;
	}

	/**
	 * Determines an item ID have in items.
	 *
	 * @param  Booking_Item|int $item_id Booking item ID.
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
		$item = Factory::get_booking_item( $item_id );
		if ( ! $item ) {
			return;
		}

		foreach ( $this->get_all_items() as $_item ) {
			if ( $item->get_id() === $_item->get_id() ) {
				return $item;
			}
		}
	}

	/**
	 * Returns all items.
	 *
	 * @return Collection
	 */
	public function get_all_items() {
		$this->setup_booking_items();

		return $this->items;
	}

	/**
	 * Get items by type.
	 *
	 * @param  string $type Item type.
	 * @return Collection
	 */
	public function get_items( $type ) {
		return $this->get_all_items()->filter(function( $item ) use ( $type ) {
			return $item->get_type() === $type;
		});
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
	 * Returns collection of service items.
	 *
	 * @return Collection
	 */
	public function get_service_items() {
		return $this->get_items( 'service_item' );
	}

	/**
	 * Gets the count of booking items of a certain type.
	 *
	 * @param  string $item_type Item type.
	 * @return string
	 */
	public function get_item_count( $item_type = 'line_item' ) {
		$count = 0;

		foreach ( $this->get_items( $item_type ) as $item ) {
			$count += $item->get_quantity();
		}

		return apply_filters( $this->prefix( 'get_item_count' ), $count, $item_type, $this );
	}

	/**
	 * Save all boooking items which are part of this boooking.
	 */
	protected function save_items() {
		foreach ( $this->items_to_delete as $delete_item ) {
			$delete_item->delete();
		}

		foreach ( $this->get_all_items() as $item ) {
			$item->set_booking_id( $this->get_id() );
			$item->save();
		}

		$this->items_to_delete = [];
	}

	/**
	 * Setup booking items from database.
	 *
	 * @return void
	 */
	protected function setup_booking_items() {
		if ( ! $this->exists() ) {
			return;
		}

		if ( $this->did_setup_items ) {
			return;
		}

		$this->did_setup_items = true;

		// Try get in the cache first.
		$db_items = wp_cache_get( $this->get_id(), 'awebooking_cache_booking_items' );

		if ( false === $db_items ) {
			global $wpdb;

			$db_items = $wpdb->get_results(
				$wpdb->prepare( "SELECT * FROM `{$wpdb->prefix}awebooking_booking_items` WHERE `booking_id` = %d ORDER BY `booking_item_id`", $this->get_id() ),
				ARRAY_A
			);

			// Cache each single item.
			foreach ( $db_items as &$item ) {
				// Santize before cache current booking-item.
				$item['booking_id'] = (int) $item['booking_id'];
				$item['booking_item_id'] = (int) $item['booking_item_id'];

				wp_cache_add( $item['booking_item_id'], $item, 'awebooking_cache_booking_item' );
			}

			wp_cache_add( $this->get_id(), $db_items, 'awebooking_cache_booking_items' );
		}

		$items = awebooking_map_instance( $db_items, [ Factory::class, 'get_booking_item' ] );

		if ( 0 === count( $this->items ) ) {
			$this->items = new Collection( $items );
		} else {
			foreach ( $items as $_item ) {
				$this->items->push( $_item );
			}
		}
	}
}
