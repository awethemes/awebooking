<?php
namespace AweBooking\Cart;

use Closure;
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
	 * Subtotal
	 *
	 * @var float
	 */
	public $subtotal = 0.00;

	/**
	 * Total
	 *
	 * @var float
	 */
	public $total = 0.00;

	/**
	 * Total cart tax.
	 *
	 * @var float
	 */
	public $tax_total = 0.00;

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
	 * @param Buyable   $item
	 * @param int       $qty
	 * @param array     $options
	 * @return Cart_Item
	 */
	public function add( Buyable $item, $qty = null, array $options = [] ) {
		$cart_item = Cart_Item::from_buyable( $item, $options );

		$contents = $this->get_contents();

		if ( $contents->has( $cart_item->row_id ) ) {
			$cart_item->set_quantity(
				$contents->get( $cart_item->row_id )->quantity + 1
			);
		} else {
			$cart_item->set_quantity( $qty );
		}

		$contents->put( $cart_item->row_id, $cart_item );

		$this->session->put( static::CART_CONTENTS, $contents->to_array() );

		return $cart_item;
	}

	/**
	 * Remove the cart item with the given row_id from the cart.
	 *
	 * @param string $row_id
	 * @return void
	 */
	public function remove( $row_id ) {
		$cart_item = $this->get( $row_id );

		$contents = $this->get_contents();
		$contents->pull( $cart_item->row_id );

		$this->session->put( static::CART_CONTENTS, $contents->to_array() );
	}

	/**
	 * Get a cart item from the cart by its row_id.
	 *
	 * @param string $row_id
	 * @return Cart_Item
	 */
	public function get( $row_id ) {
		$content = $this->get_contents();

		if ( ! $content->has( $row_id ) ) {
			throw new Invalid_RowID_Exception;
		}

		return $content->get( $row_id );
	}

	/**
	 * Search the cart content for a cart item matching the given search closure.
	 *
	 * @param \Closure $search
	 * @return \Illuminate\Support\Collection
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
		return $this->get_contents()->sum( 'qty' );
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
	 * @access public
	 * @return void
	 */
	public function get_contents_from_session() {
		$session_contents = (array) $this->session->get( static::CART_CONTENTS, [] );

		$contents = [];
		foreach ( $session_contents as $row_id => $cart_item_array ) {
			if ( ! is_array( $cart_item_array ) || ! isset( $cart_item_array['row_id'] ) ) {
				continue;
			}

			$cart_item = Cart_Item::from_array( $cart_item_array );
			$buyable_item = $cart_item->model();

			if ( ! $cart_item->get_id() || ! $buyable_item instanceof Buyable ) {
				continue;
			}

			// TODO: Add notice about remove item cant be purchase anymore.
			if ( ! $buyable_item->is_purchasable() ) {
				continue;
			}

			$contents[ $row_id ] = $cart_item;
		}

		$this->contents = apply_filters( 'awebooking/cart_contents', new Collection( $contents ) );

		$this->session->put( static::CART_CONTENTS, $this->contents->to_array() );

		do_action( 'awebooking/get_contents_from_session', $this );
	}
}
