<?php
namespace AweBooking\Cart;

use Closure;
use AweBooking\Pricing\Price;
use AweBooking\Support\Collection;
use Awethemes\WP_Session\WP_Session;
use AweBooking\Cart\Exceptions\Unknown_Model_Exception;
use AweBooking\Cart\Exceptions\Invalid_RowID_Exception;
use AweBooking\Cart\Exceptions\Cart_Already_Stored_Exception;

class Cart {
	/* Constants */
	const CART_CONTENTS = 'cart_contents';

	/**
	 * Instance of the Session.
	 *
	 * @var WP_Session
	 */
	protected $session;

	/**
	 * The cart contents.
	 *
	 * @var Collection
	 */
	protected $contents;

	/**
	 * Discount codes.
	 *
	 * TODO: ...
	 *
	 * @var array
	 */
	public $discounts = [];

	/**
	 * An array of fees.
	 *
	 * TODO: ...
	 *
	 * @var array
	 */
	public $fees = [];

	/**
	 * Cart constructor.
	 *
	 * @param WP_Session $session WP_Session implementation.
	 */
	public function __construct( WP_Session $session ) {
		$this->session = $session;
	}

	/**
	 * Add an item to the cart.
	 *
	 * @param Buyable $item
	 * @param int     $quantity
	 * @param array   $options
	 * @return Item
	 */
	public function add( Buyable $item, $quantity = null, array $options = [] ) {
		$cart_item = Item::from_buyable( $item, $options );
		$contents = $this->get_contents();

		if ( $contents->has( $cart_item->row_id ) ) {
			$cart_item->set_quantity(
				$contents->get( $cart_item->row_id )->quantity + 1
			);
		} else {
			$cart_item->set_quantity( $quantity );
		}

		$contents->put( $cart_item->row_id, $cart_item );
		$this->store_cart_contents();

		return $cart_item;
	}

	/**
	 * Remove the cart item with the given row_id from the cart.
	 *
	 * @param  string $row_id The row ID.
	 * @return void
	 */
	public function remove( $row_id ) {
		$cart_item = $this->get( $row_id );

		$contents = $this->get_contents();
		$contents->pull( $cart_item->row_id );

		$this->store_cart_contents();
	}

	/**
	 * Destroy the current cart.
	 *
	 * @return void
	 */
	public function destroy() {
		$this->session->remove( static::CART_CONTENTS );
	}

	/**
	 * Get a cart item from the cart by its row_id.
	 *
	 * @param  string $row_id The row ID.
	 * @return Item
	 */
	public function get( $row_id ) {
		$content = $this->get_contents();

		if ( ! $content->has( $row_id ) ) {
			throw new Invalid_RowID_Exception();
		}

		return $content->get( $row_id );
	}

	/**
	 * Search the cart content for a cart item matching the given search closure.
	 *
	 * @param  Closure $search Search logic.
	 * @return Items
	 */
	public function search( Closure $search ) {
		return $this->get_contents()->filter( $search );
	}

	/**
	 * Get the number of items in the cart.
	 *
	 * @return int|float
	 */
	public function count() {
		return $this->get_contents()->sum->get_quantity();
	}

	/**
	 * Get the total price of the items in the cart.
	 *
	 * @return string
	 */
	public function total() {
		return $this->get_contents()->reduce(
			function ( Price $total, Item $cart_item ) {
				return $total->add( $cart_item->get_total() );
			}, Price::zero()
		);
	}

	/**
	 * Get the total tax of the items in the cart.
	 *
	 * @return float
	 */
	public function tax() {
		return $this->get_contents()->reduce(
			function ( Price $tax, Item $cart_item ) {
				return $tax->add( $cart_item->get_tax_total() );
			}, Price::zero()
		);
	}

	/**
	 * Get the subtotal (total - tax) of the items in the cart.
	 *
	 * @return Price
	 */
	public function subtotal() {
		return $this->get_contents()->reduce(
			function ( Price $sub_total, Item $cart_item ) {
				return $sub_total->add( $cart_item->get_subtotal() );
			}, Price::zero()
		);
	}

	/**
	 * Get the cart contents.
	 *
	 * @return Collection
	 */
	public function get_contents() {
		if ( is_null( $this->contents ) ) {
			$this->get_contents_from_session();
		}

		return $this->contents;
	}

	/**
	 * Populate the cart with the data stored in the session.
	 *
	 * @return void
	 */
	protected function get_contents_from_session() {
		$session_contents = (array) $this->session->get( static::CART_CONTENTS, [] );

		$contents = [];
		foreach ( $session_contents as $row_id => $cart_item_array ) {
			if ( ! is_array( $cart_item_array ) || ! isset( $cart_item_array['row_id'] ) ) {
				continue;
			}

			$cart_item = Item::from_array( $cart_item_array );
			$buyable_item = $cart_item->model();

			if ( ! $cart_item->get_id() || ! $buyable_item instanceof Buyable ) {
				continue;
			}

			// TODO: Add notice about remove item cant be purchase anymore.
			if ( ! $buyable_item->is_purchasable( $cart_item->options ) ) {
				continue;
			}

			$contents[ $row_id ] = $cart_item;
		}

		$this->contents = apply_filters( 'awebooking/cart/contents', new Cart_Items( $contents ) );
		$this->store_cart_contents();

		do_action( 'awebooking/cart/get_contents_from_session', $this );
	}

	/**
	 * Set cart instance in session
	 *
	 * @return void
	 */
	public function store_cart_contents() {
		$this->session->put( static::CART_CONTENTS, $this->contents->to_array() );
	}
}
