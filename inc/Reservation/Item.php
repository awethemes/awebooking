<?php
namespace AweBooking\Reservation;

use AweBooking\Support\Optional;
use AweBooking\Support\Collection;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Arr;

class Item implements Arrayable, \ArrayAccess, \JsonSerializable {
	/**
	 * The row ID of the reservation item.
	 *
	 * @var string
	 */
	protected $row_id;

	/**
	 * The ID represent for the reservation item.
	 *
	 * @var int
	 */
	protected $id = 0;

	/**
	 * The name of reservation item.
	 *
	 * @var string
	 */
	protected $name = '';

	/**
	 * The price without TAX of the reservation item.
	 *
	 * @var float|int
	 */
	protected $price = 0;

	/**
	 * The quantity of the item.
	 *
	 * @var int
	 */
	protected $quantity = 1;

	/**
	 * Taxable this item?
	 *
	 * @var bool
	 */
	protected $taxable = false;

	/**
	 * Is the price includes tax?
	 *
	 * @var bool
	 */
	protected $price_includes_tax = false;

	/**
	 * The tax rate (percent) of the item.
	 *
	 * @var float
	 */
	protected $tax_rate = 0;

	/**
	 * The tax rate name for display.
	 *
	 * @var string
	 */
	protected $tax_name = 'Tax';

	/**
	 * The options of the item.
	 *
	 * @var \AweBooking\Support\Collection
	 */
	protected $options;

	/**
	 * The item data.
	 *
	 * @var mixed
	 */
	protected $data;

	/**
	 * The FQN of the associated model.
	 *
	 * @var string|null
	 */
	protected $associated_model;

	/**
	 * Cache the resolved models.
	 *
	 * @var array
	 */
	protected static $resolved_models = [];

	/**
	 * Constructor.
	 *
	 * @param array $attributes The item attributes.
	 */
	public function __construct( array $attributes = [] ) {
		foreach ( array_keys( $this->attributes() ) as $key ) {
			if ( array_key_exists( $key, $attributes ) ) {
				$this->set( $key, $attributes[ $key ] );
			}
		}

		if ( is_null( $this->options ) ) {
			$this->set_options( [] );
		}

		if ( ! $this->row_id ) {
			$this->row_id = static::generate_row_id( $this->id, $this->options );
		}
	}

	/**
	 * Returns the item attributes.
	 *
	 * @return array
	 */
	public function attributes() {
		return get_object_vars( $this );
	}

	/**
	 * Update the item from an array.
	 *
	 * @param  array $attributes The item attributes.
	 * @return $this
	 */
	public function update( array $attributes ) {
		foreach ( array_keys( $this->attributes() ) as $key ) {
			if ( array_key_exists( $key, $attributes ) ) {
				$this->set( $key, $attributes[ $key ] );
			}
		}

		$this->row_id = static::generate_row_id( $this->id, $this->options );

		return $this;
	}

	/**
	 * Get a piece of data set on the item.
	 *
	 * @param  string $key The key name.
	 * @return mixed
	 */
	public function get( $key ) {
		if ( array_key_exists( $key, $this->attributes() ) ) {
			return $this->{$key};
		}

		// Gets the virutal property.
		if ( in_array( $key, [ 'single_price', 'single_price_exc_tax', 'total_price', 'total_price_exc_tax', 'single_tax', 'total_tax' ] ) ) {
			return $this->{"get_{$key}"}();
		}

		// 500
		// 10%
		// 1. 450
		// 2. 560

		return $this->get_option( $key );
	}

	/**
	 * Set a piece of data on the item.
	 *
	 * @param  string $key   The key name.
	 * @param  mixed  $value The value.
	 * @return $this
	 */
	public function set( $key, $value ) {
		if ( 'options' === $key ) {
			$this->set_options( $value );
		} elseif ( array_key_exists( $key, $this->attributes() ) ) {
			$this->{$key} = $this->sanitize_prop( $value, $key );
		} else {
			$this->set_option( $key, $value );
		}

		return $this;
	}

	/**
	 * Gets the row ID of the item.
	 *
	 * @return string
	 */
	public function get_row_id() {
		return $this->row_id;
	}

	/**
	 * Gets the ID of the item.
	 *
	 * @return int
	 */
	public function get_id() {
		return $this->id;
	}

	/**
	 * Gets the name of the item.
	 *
	 * @return int
	 */
	public function get_name() {
		return $this->name;
	}

	/**
	 * Returns the quantity of the item.
	 *
	 * @return int
	 */
	public function get_quantity() {
		return $this->quantity;
	}

	/**
	 * Increment the quantity of the item.
	 *
	 * @param int $amount The amount to increment.
	 */
	public function increment( $amount = 1 ) {
		$this->quantity += $amount;

		return $this;
	}

	/**
	 * Gets the tax rate (percent).
	 *
	 * @return int|float
	 */
	public function get_tax_rate() {
		return $this->tax_rate;
	}

	/**
	 * Gets the single price of the item including tax.
	 *
	 * @return \AweBooking\Support\Decimal
	 */
	public function get_single_price() {
		return abrs_decimal( $this->price )->surcharge( $this->tax_rate );
	}

	/**
	 * Gets the single price of the item excluding tax.
	 *
	 * @return \AweBooking\Support\Decimal
	 */
	public function get_single_price_exc_tax() {
		return abrs_decimal( $this->price );
	}

	/**
	 * Gets the total price of the item including tax.
	 *
	 * @return \AweBooking\Support\Decimal
	 */
	public function get_total_price() {
		return $this->get_single_price()->mul( $this->quantity );
	}

	/**
	 * Gets the total price of the item excluding tax.
	 *
	 * @return \AweBooking\Support\Decimal
	 */
	public function get_total_price_exc_tax() {
		return $this->get_single_price_exc_tax()->mul( $this->quantity );
	}

	/**
	 * Gets the single tax value of the item.
	 *
	 * @return \AweBooking\Support\Decimal
	 */
	public function get_single_tax() {
		return abrs_decimal( $this->price )->to_percentage( $this->tax_rate );
	}

	/**
	 * Gets the total tax for the item.
	 *
	 * @return \AweBooking\Support\Decimal
	 */
	public function get_total_tax() {
		return $this->get_single_tax()->mul( $this->quantity );
	}

	/**
	 * Gets the options of the item.
	 *
	 * @return \AweBooking\Support\Collection
	 */
	public function get_options() {
		return $this->options;
	}

	/**
	 * Sets the options of the item.
	 *
	 * @param  array $options An array of options.
	 * @return $this
	 */
	public function set_options( $options ) {
		$this->options = new Collection( $options );

		return $this;
	}

	/**
	 * Gets piece of options set on the item.
	 *
	 * @param  string $key     The option key name.
	 * @param  mixed  $default Optional, default value if $key is not set.
	 * @return mixed
	 */
	public function get_option( $key, $default = null ) {
		return Arr::get( $this->options->all(), $key, $default );
	}

	/**
	 * Sets a option on the item.
	 *
	 * @param  string $key   The option key name.
	 * @param  mixed  $value The option value.
	 * @return $this
	 */
	public function set_option( $key, $value ) {
		if ( $value = $this->sanitize_option( $value, $key ) ) {
			$this->options->put( $key, $value );
		}

		return $this;
	}

	/**
	 * Gets the item data.
	 *
	 * @return mixed
	 */
	public function data() {
		return $this->data;
	}

	/**
	 * Gets the item data.
	 *
	 * @return mixed
	 */
	public function get_data() {
		return $this->data;
	}

	/**
	 * Sets the data represent for the item.
	 *
	 * @param  mixed $data The data.
	 * @return $this
	 */
	public function set_data( $data ) {
		$this->data = $data;

		return $this;
	}

	/**
	 * Associate the item with the given model.
	 *
	 * @param  \AweBooking\Model\Model|string $model The associate model name.
	 * @return $this
	 */
	public function associate( $model ) {
		$this->associated_model = is_string( $model ) ? $model : get_class( $model );

		return $this;
	}

	/**
	 * Returns associated model object.
	 *
	 * @return \AweBooking\Model\Model|\AweBooking\Support\Optional
	 */
	public function model() {
		$row_id = $this->get_row_id();

		if ( array_key_exists( $row_id, static::$resolved_models ) ) {
			return static::$resolved_models[ $row_id ];
		}

		if ( ( $model = $this->associated_model ) && class_exists( $model ) ) {
			$resolved = new $model( $this->id );
		} else {
			$resolved = new Optional( null );
		}

		return static::$resolved_models[ $row_id ] = $resolved;
	}

	/**
	 * Generate a unique ID of the item.
	 *
	 * @param int              $id      The ID.
	 * @param array|Collection $options The options.
	 * @return string
	 */
	public static function generate_row_id( $id, $options ) {
		$options = array_filter(
			$options instanceof Collection ? $options->all() : (array) $options
		);

		ksort( $options );

		return sha1( $id . serialize( $options ) );
	}

	/**
	 * Sanitize property before adding.
	 *
	 * @param  mixed  $value The value.
	 * @param  string $key   The property key name.
	 * @return mixed
	 */
	protected function sanitize_prop( $value, $key ) {
		switch ( $key ) {
			case 'tax':
			case 'price':
			case 'tax_rate':
				return abrs_sanitize_decimal( $value );
			case 'model':
			case 'associated_model':
				return is_string( $value ) ? $value : get_class( $value );
			case 'quantity':
				return max( 1, (int) $value );
			case 'id':
				return absint( $value );
		}

		return abrs_clean( $value );
	}

	/**
	 * Sanitize option before adding.
	 *
	 * @param  mixed  $value The option value.
	 * @param  string $key   The option key name.
	 * @return mixed
	 */
	protected function sanitize_option( $value, $key ) {
		return abrs_clean( $value );
	}

	/**
	 * Convert the object to an array.
	 *
	 * @return array
	 */
	public function to_array() {
		$arr = $this->attributes();

		$arr['options'] = $this->options->all();
		unset( $arr['data'] );

		return $arr;
	}

	/**
	 * {@inheritdoc}
	 */
	public function toArray() {
		return $this->to_array();
	}

	/**
	 * Convert the object into something JSON serializable.
	 *
	 * @return array
	 */
	public function jsonSerialize() {
		return $this->to_array();
	}

	/**
	 * Whether the given offset exists.
	 *
	 * @param  string $offset The offset name.
	 * @return bool
	 */
	public function offsetExists( $offset ) {
		if ( array_key_exists( $offset, $this->attributes() ) ) {
			return true;
		}

		return $this->options->has( $offset );
	}

	/**
	 * Fetch the offset.
	 *
	 * @param  string $offset The offset name.
	 * @return mixed
	 */
	public function offsetGet( $offset ) {
		return $this->get( $offset );
	}

	/**
	 * Assign the offset.
	 *
	 * @param  string $offset The offset name.
	 * @param  mixed  $value  The offset value.
	 * @return void
	 */
	public function offsetSet( $offset, $value ) {
		$this->set( $offset, $value );
	}

	/**
	 * Unset the offset.
	 *
	 * @param  mixed $offset The offset name.
	 * @return void
	 */
	public function offsetUnset( $offset ) {
		unset( $this->options[ $offset ] );
	}

	/**
	 * Get a piece of data set on the item.
	 *
	 * @param string $key The getter key.
	 * @return mixed
	 */
	public function __get( $key ) {
		return $this->get( $key );
	}

	/**
	 * Set a piece of data on the item.
	 *
	 * @param string $key   The setter key.
	 * @param mixed  $value The setter value.
	 */
	public function __set( $key, $value ) {
		$this->set( $key, $value );
	}
}
