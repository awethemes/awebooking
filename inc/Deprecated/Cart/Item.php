<?php
namespace AweBooking\Cart;

use AweBooking\Pricing\Price;
use AweBooking\Support\Optional;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Contracts\Support\Arrayable;

class Item implements Arrayable, Jsonable {
	/**
	 * The row ID of the cart item.
	 *
	 * @var string
	 */
	protected $row_id;

	/**
	 * The ID of the cart item.
	 *
	 * @var int
	 */
	protected $id;

	/**
	 * The price without TAX of the cart item.
	 *
	 * @var Price
	 */
	protected $price;

	/**
	 * The options for this cart item.
	 *
	 * @var Options
	 */
	protected $options;

	/**
	 * The quantity for this cart item.
	 *
	 * @var int
	 */
	protected $quantity = 1;

	/**
	 * The tax rate for the cart item.
	 *
	 * @var float
	 */
	protected $tax_rate = 0.00;

	/**
	 * The FQN of the associated model.
	 *
	 * @var string|null
	 */
	protected $associated_model = null;

	/**
	 * Create a new instance from a Buyable.
	 *
	 * @param Buyable $item    Buyable item implements.
	 * @param array   $options //.
	 * @return static
	 */
	public static function from_buyable( Buyable $item, array $options = [] ) {
		$options = Options::make( $options );

		$cart_item = new static(
			$item->get_buyable_identifier( $options ),
			$item->get_buyable_price( $options ),
			$options
		);

		$cart_item->associate( $item );

		return $cart_item;
	}

	/**
	 * Create a new instance from the given array.
	 *
	 * @param array $attributes An array attributes of a valid cart item.
	 * @return static
	 */
	public static function from_array( array $attributes ) {
		$options = isset( $attributes['options'] ) ? $attributes['options'] : [];
		$row_id  = isset( $attributes['row_id'] ) ? $attributes['row_id'] : null;

		$cart_item = new static(
			$attributes['id'], new Price( $attributes['price'] ), $options, $row_id
		);

		if ( isset( $attributes['quantity'] ) && $attributes['quantity'] > 0 ) {
			$cart_item->set_quantity( $attributes['quantity'] );
		}

		if ( isset( $attributes['associate'] ) ) {
			$cart_item->associate( $attributes['associate'] );
		}

		return $cart_item;
	}

	/**
	 * Cart item constructor.
	 *
	 * @param int|string $id      The ID of item.
	 * @param Price      $price   The Price of item.
	 * @param array      $options The cart item options.
	 */
	public function __construct( $id, Price $price, $options = [], $row_id = null ) {
		$this->id      = $id;
		$this->price   = $price;
		$this->options = Options::make( $options );
		$this->row_id  = $row_id ?: $this->generate_row_id( $id, $this->options->to_array() );
	}

	/**
	 * Returns ID of the cart item.
	 *
	 * @return int
	 */
	public function get_id() {
		return $this->id;
	}

	/**
	 * Returns the row ID of the cart item.
	 *
	 * @return string
	 */
	public function get_row_id() {
		return $this->row_id;
	}

	/**
	 * Returns the price without TAX.
	 *
	 * @return Price
	 */
	public function get_price() {
		return $this->price;
	}

	/**
	 * Set the price for this cart item.
	 *
	 * @param  int $price The price amount.
	 * @return $this
	 */
	public function set_price( Price $price ) {
		$this->price = $price;

		return $this;
	}

	/**
	 * Returns the options for this cart item.
	 *
	 * @return int
	 */
	public function get_options() {
		return $this->options;
	}

	/**
	 * Returns the quantity for this cart item.
	 *
	 * @return int
	 */
	public function get_quantity() {
		return $this->quantity;
	}

	/**
	 * Set the quantity for this cart item.
	 *
	 * @param  int $quantity The quantity number, required minimum 1.
	 * @return $this
	 */
	public function set_quantity( $quantity ) {
		$this->quantity = max( 1,  (int) $quantity );

		return $this;
	}

	/**
	 * Returns the tax rate.
	 *
	 * @return int|float
	 */
	public function get_tax_rate() {
		return $this->tax_rate;
	}

	/**
	 * Set the tax rate.
	 *
	 * @param  int|float $tax_rate The tax rate.
	 * @return $this
	 */
	public function set_tax_rate( $tax_rate ) {
		$this->tax_rate = max( 0, floatval( $tax_rate ) );

		return $this;
	}

	/**
	 * Returns the price of TAX.
	 *
	 * @return Price
	 */
	public function get_tax() {
		return $this->get_price()->multiply(
			$this->get_tax_rate() / 100
		);
	}

	/**
	 * Returns the price with TAX.
	 *
	 * @return Price
	 */
	public function get_price_with_tax() {
		return $this->get_price()->add(
			$this->get_tax()
		);
	}

	/**
	 * Returns the total of TAX.
	 *
	 * @return Price
	 */
	public function get_tax_total() {
		return $this->get_tax()->multiply(
			$this->get_quantity()
		);
	}

	/**
	 * Returns the subtotal.
	 *
	 * Subtotal is price for whole Item without TAX.
	 *
	 * @return string
	 */
	public function get_subtotal() {
		return $this->get_price()->multiply(
			$this->get_quantity()
		);
	}

	/**
	 * Returns the total costs.
	 *
	 * Total is price for whole Item with TAX.
	 *
	 * @return string
	 */
	public function get_total() {
		return $this->get_price_with_tax()->multiply(
			$this->get_quantity()
		);
	}

	/**
	 * Associate the cart item with the given model.
	 *
	 * @param  string|WP_Object $model The associate model name.
	 * @return $this
	 */
	public function associate( $model ) {
		$this->associated_model = is_string( $model ) ? $model : get_class( $model );

		return $this;
	}

	/**
	 * Returns associated model object.
	 *
	 * @return WP_Object|Optional|mixed
	 */
	public function model() {
		$model = $this->associated_model;

		if ( $model && class_exists( $model ) ) {
			return new $model( $this->get_id() );
		}

		return new Optional( null );
	}

	/**
	 * Generate a unique id for the cart item.
	 *
	 * @param  string $id      Cart item ID.
	 * @param  array  $options Cart item options.
	 * @return string
	 */
	protected function generate_row_id( $id, array $options ) {
		ksort( $options );

		return md5( $id . serialize( $options ) . time() );
	}

	/**
	 * Get an attribute from the cart item.
	 *
	 * @param  string $attribute Getter attribute name.
	 * @return mixed
	 */
	public function __get( $attribute ) {
		$method = "get_{$attribute}";

		if ( method_exists( $this, $method ) ) {
			return $this->{$method}();
		}
	}

	/**
	 * Convert the object to its JSON representation.
	 *
	 * @param  int $options The `json_encode` options.
	 * @return string
	 */
	public function toJson( $options = 0 ) {
		return json_encode( $this->toArray(), $options );
	}

	/**
	 * Get the instance as an array.
	 *
	 * @return array
	 */
	public function toArray() {
		return [
			'row_id'    => $this->row_id,
			'id'        => $this->id,
			'price'     => $this->price->get_amount(),
			'quantity'  => $this->quantity,
			'options'   => $this->options->toArray(),
			'associate' => $this->associated_model,
		];
	}
}
