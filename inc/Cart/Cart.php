<?php
namespace AweBooking\Cart;

use Closure;
use AweBooking\Session\Session;
use Illuminate\Support\Collection;

use Gloudemans\Shoppingcart\Exceptions\UnknownModelException;
use Gloudemans\Shoppingcart\Exceptions\InvalidRowIDException;
use Gloudemans\Shoppingcart\Exceptions\CartAlreadyStoredException;

class Cart {
	/* Constants */
	const SESSION_NAME = 'cart';

	/**
	 * Instance of the Session.
	 *
	 * @var Session
	 */
	protected $session;

	/**
	 * The cart contents.
	 *
	 * @var Collection
	 */
	protected $contents;

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
	 * @param Session $session Session Store implements.
	 */
	public function __construct( Session $session ) {
		$this->session  = $session;
	}

	/**
	 * Add an item to the cart.
	 *
	 * @param mixed     $id
	 * @param int|float $qty
	 * @param array     $options
	 * @return Cart_Item
	 */
	public function add( Buyable $id, $qty = null, array $options = [] ) {
		$cart_item = $this->create_cart_item( $id, $qty, $options );

		$contents = $this->get_contents();

		if ( $contents->has( $cart_item->row_id ) ) {
			$cart_item->qty += $contents->get( $cart_item->row_id )->qty;
		}

		$contents->put( $cart_item->row_id, $cart_item );

		$this->session->put( static::SESSION_NAME, $contents );

		return $cart_item;
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
		$contents = new Collection( $this->session->get( static::SESSION_NAME, [] ) );

		// TOOD: Validate the contents from session.
		$this->contents = $contents;
	}

	/**
	 * Create a new Cart_Item from the supplied attributes.
	 *
	 * @param mixed     $id
	 * @param mixed     $name
	 * @param int|float $qty
	 * @param float     $price
	 * @param array     $options
	 * @return Cart_Item
	 */
	protected function create_cart_item( $id, $qty, $options ) {
		if ( $id instanceof Buyable ) {
			$cart_item = Cart_Item::from_buyable( $id, $options );
			$cart_item->set_quantity( $qty ?: 1 );
			$cart_item->associate( $id );
		} elseif ( is_array( $id ) ) {
			$cart_item = Cart_Item::from_array( $id );
			$cart_item->set_quantity( $id['qty'] );
		}

		// $cart_item->set_tax_rate( 0 );

		return $cart_item;
	}
}
