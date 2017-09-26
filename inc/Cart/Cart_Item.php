<?php
namespace AweBooking\Cart;

use AweBooking\Pricing\Price;
use AweBooking\Support\Optional;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Contracts\Support\Arrayable;

class Cart_Item implements Arrayable, Jsonable {
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
	 * The name of the cart item.
	 *
	 * @var string
	 */
	protected $name;

	/**
	 * The options for this cart item.
	 *
	 * @var Cart_Item_Options
	 */
	protected $options;

	/**
	 * The price without TAX of the cart item.
	 *
	 * @var Price
	 */
	public $price;

	/**
	 * The quantity for this cart item.
	 *
	 * @var int
	 */
	protected $qty = 1;

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
	 * @param array   $options
	 * @return static
	 */
	public static function from_buyable( Buyable $item, array $options = [] ) {
		return new static(
			$item->get_buyable_identifier( $options ),
			$item->get_buyable_description( $options ),
			$item->get_buyable_price( $options ),
			$options
		);
	}

	/**
	 * Create a new instance from the given array.
	 *
	 * @param array $attributes
	 * @return static
	 */
	public static function from_array( array $attributes ) {
		$options = array_get( $attributes, 'options', [] );

		return new static($attributes['id'], $attributes['name'], $attributes['price'], $options);
	}

	/**
	 * Cart item constructor.
	 *
	 * @param int|string $id      The ID of item.
	 * @param string     $name    The name of item.
	 * @param Price      $price   The Price of item.
	 * @param array      $options The cart item options.
	 */
	public function __construct( $id, $name, Price $price, array $options = [] ) {
		$this->id      = $id;
		$this->name    = $name;
		$this->price   = $price;

		$this->options = new Cart_Item_Options( $options );
		$this->row_id  = $this->generate_row_id( $id, $options );
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
	 * Returns the name of the cart item.
	 *
	 * @return int
	 */
	public function get_name() {
		return $this->name;
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
		return $this->qty;
	}

	/**
	 * Set the quantity for this cart item.
	 *
	 * @param  int $qty The quantity number, required minimum 1.
	 * @return $this
	 */
	public function set_quantity( $qty ) {
		$this->qty = max( 1,  (int) $qty );

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
	 * Returns the price without TAX.
	 *
	 * @return Price
	 */
	public function get_price() {
		return $this->price;
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
	 * Subtotal is price for whole Cart_Item without TAX.
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
	 * Total is price for whole Cart_Item with TAX.
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

		return md5( $id . serialize( $options ) );
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
	 * TODO: ...
	 *
	 * @return array
	 */
	public function toArray() {
		return [
			'row_id'   => $this->row_id,
			'id'       => $this->id,
			'name'     => $this->name,
			'qty'      => $this->qty,
			'price'    => $this->price->get_amount(),
			'options'  => $this->options->toArray(),
			'tax'      => $this->tax->get_amount(),
			'subtotal' => $this->subtotal->get_amount(),
		];
	}
}
