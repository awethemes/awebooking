<?php
namespace AweBooking\Session;

use Countable;
use ArrayAccess;
use SessionHandlerInterface;
use Illuminate\Support\Arr;

class Store implements Session, ArrayAccess, Countable {
	/**
	 * The session ID.
	 *
	 * @var string
	 */
	protected $id;

	/**
	 * The session name.
	 *
	 * @var string
	 */
	protected $name;

	/**
	 * The session handler implementation.
	 *
	 * @var SessionHandlerInterface
	 */
	protected $handler;

	/**
	 * The session attributes.
	 *
	 * @var array
	 */
	protected $attributes = [];

	/**
	 * Session store started status.
	 *
	 * @var bool
	 */
	protected $started = false;

	/**
	 * Create a new session instance.
	 *
	 * @param  string                  $name    Session name.
	 * @param  SessionHandlerInterface $handler The session handler implementation.
	 * @param  string|null             $id      Optional, session ID.
	 * @return void
	 */
	public function __construct( $name, SessionHandlerInterface $handler, $id = null ) {
		$this->set_id( $id );
		$this->name = $name;
		$this->handler = $handler;
	}

	/**
	 * Start the session, reading the data from a handler.
	 *
	 * @return void
	 */
	public function start() {
		$data = $this->handler->read( $this->get_id() );

		if ( false !== $data && ! is_null( $data ) && is_array( $data ) ) {
			$this->attributes = $data;
		}

		$this->started = true;
	}

	/**
	 * Save the session data to storage.
	 *
	 * @return void
	 */
	public function save() {
		if ( ! $this->started ) {
			return;
		}

		$this->age_flash_data();

		$this->handler->write(
			$this->get_id(), $this->attributes
		);

		$this->started = false;
	}

	/**
	 * Get all of the session data.
	 *
	 * @return array
	 */
	public function all() {
		return $this->attributes;
	}

	/**
	 * Checks if a key exists.
	 *
	 * @param  string|array $key Key name or an array keys.
	 * @return bool
	 */
	public function exists( $key ) {
		return ! collect( is_array( $key ) ? $key : func_get_args() )->contains(
			function ( $key ) {
				return ! Arr::exists( $this->attributes, $key );
			}
		);
	}

	/**
	 * Checks if a key is present and not null.
	 *
	 * @param  string|array $key Key name or an array keys.
	 * @return bool
	 */
	public function has( $key ) {
		return ! collect( is_array( $key ) ? $key : func_get_args() )->contains(
			function ( $key ) {
				return is_null( $this->get( $key ) );
			}
		);
	}

	/**
	 * Get an item from the session with "dot" notation.
	 *
	 * @param  string $key     Session key name.
	 * @param  mixed  $default Default value.
	 * @return mixed
	 */
	public function get( $key, $default = null ) {
		return Arr::get( $this->attributes, $key, $default );
	}

	/**
	 * Get the value of a given key and then forget it.
	 *
	 * @param  string $key     Session key name.
	 * @param  string $default Default value.
	 * @return mixed
	 */
	public function pull( $key, $default = null ) {
		return Arr::pull( $this->attributes, $key, $default );
	}

	/**
	 * Put a key/value pair or array of key/value pairs in the session.
	 *
	 * @param  string|array $key   An array of key/value pairs or string key name.
	 * @param  mixed        $value If $key is string, use this as value.
	 * @return void
	 */
	public function put( $key, $value = null ) {
		if ( ! is_array( $key ) ) {
			$key = [ $key => $value ];
		}

		foreach ( $key as $array_key => $array_value ) {
			Arr::set( $this->attributes, $array_key, $array_value );
		}
	}

	/**
	 * Push a value onto a session array.
	 *
	 * @param  string $key   Session key name.
	 * @param  mixed  $value Session key value to push.
	 * @return void
	 */
	public function push( $key, $value ) {
		$array = $this->get( $key, [] );

		$array[] = $value;

		$this->put( $key, $array );
	}

	/**
	 * Increment the value of an item in the session.
	 *
	 * @param  string $key    Session key name.
	 * @param  int    $amount Number amount to increment.
	 * @return mixed
	 */
	public function increment( $key, $amount = 1 ) {
		$value = $this->get( $key, 0 ) + $amount;

		$this->put( $key, $value );

		return $value;
	}

	/**
	 * Decrement the value of an item in the session.
	 *
	 * @param  string $key    Session key name.
	 * @param  int    $amount Number amount to decrement.
	 * @return int
	 */
	public function decrement( $key, $amount = 1 ) {
		return $this->increment( $key, $amount * -1 );
	}

	/**
	 * Remove an item from the session, returning its value.
	 *
	 * @param  string $key Session key name to remove.
	 * @return mixed
	 */
	public function remove( $key ) {
		return Arr::pull( $this->attributes, $key );
	}

	/**
	 * Remove one or many items from the session.
	 *
	 * @param  string|array $keys An array keys or string key name to forget.
	 * @return void
	 */
	public function forget( $keys ) {
		Arr::forget( $this->attributes, $keys );
	}

	/**
	 * Replace the given session attributes entirely.
	 *
	 * @param  array $attributes An array replace attributes.
	 * @return void
	 */
	public function replace( array $attributes ) {
		$this->put( $attributes );
	}

	/**
	 * Remove all of the items from the session.
	 *
	 * @return void
	 */
	public function flush() {
		$this->attributes = [];
	}

	/**
	 * Flash a key / value pair to the session.
	 *
	 * @param  string $key
	 * @param  mixed  $value
	 * @return void
	 */
	public function flash( $key, $value = true ) {
		$this->put( $key, $value );

		$this->push( '_flash.new', $key );

		$this->remove_old_flash_data( [ $key ] );
	}

	/**
	 * Flash a key / value pair to the session for immediate use.
	 *
	 * @param  string $key
	 * @param  mixed  $value
	 * @return void
	 */
	public function now( $key, $value ) {
		$this->put( $key, $value );

		$this->push( '_flash.old', $key );
	}

	/**
	 * Reflash all of the session flash data.
	 *
	 * @return void
	 */
	public function reflash() {
		$this->merge_new_flashes( $this->get( '_flash.old', [] ) );

		$this->put( '_flash.old', [] );
	}

	/**
	 * Reflash a subset of the current flash data.
	 *
	 * @param  array|mixed $keys
	 * @return void
	 */
	public function keep( $keys = null ) {
		$this->merge_new_flashes( $keys = is_array( $keys ) ? $keys : func_get_args() );

		$this->remove_old_flash_data( $keys );
	}

	/**
	 * Merge new flash keys into the new flash array.
	 *
	 * @param  array $keys
	 * @return void
	 */
	protected function merge_new_flashes( array $keys ) {
		$values = array_unique( array_merge( $this->get( '_flash.new', [] ), $keys ) );

		$this->put( '_flash.new', $values );
	}

	/**
	 * Remove the given keys from the old flash data.
	 *
	 * @param  array $keys
	 * @return void
	 */
	protected function remove_old_flash_data( array $keys ) {
		$this->put( '_flash.old', array_diff( $this->get( '_flash.old', [] ), $keys ) );
	}

	/**
	 * Flash an input array to the session.
	 *
	 * @param  array $value An array input data.
	 * @return void
	 */
	public function flash_input( array $value ) {
		$this->flash( '_old_input', $value );
	}

	/**
	 * Determine if the session contains old input.
	 *
	 * @param  string $key Old input key name.
	 * @return bool
	 */
	public function has_old_input( $key = null ) {
		$old = $this->get_old_input( $key );

		return is_null( $key ) ? count( $old ) > 0 : ! is_null( $old );
	}

	/**
	 * Get the requested item from the flashed input array.
	 *
	 * @param  string $key     Old input key name.
	 * @param  mixed  $default Default value if old input doesn't exists.
	 * @return mixed
	 */
	public function get_old_input( $key = null, $default = null ) {
		return Arr::get( $this->get( '_old_input', [] ), $key, $default );
	}

	/**
	 * Age the flash data for the session.
	 *
	 * @access private
	 *
	 * @return void
	 */
	public function age_flash_data() {
		$this->forget( $this->get( '_flash.old', [] ) );

		$this->put( '_flash.old', $this->get( '_flash.new', [] ) );

		$this->put( '_flash.new', [] );
	}

	/**
	 * Flush the session data and regenerate the ID.
	 *
	 * @return bool
	 */
	public function invalidate() {
		$this->flush();

		return $this->regenerate( true );
	}

	/**
	 * Generate a new session identifier.
	 *
	 * @param  bool $destroy
	 * @return bool
	 */
	public function regenerate( $destroy = false ) {
		if ( $destroy ) {
			$this->handler->destroy( $this->get_id() );
		}

		$this->set_id( $this->generate_session_id() );

		return true;
	}

	/**
	 * Determine if the session has been started.
	 *
	 * @return bool
	 */
	public function is_started() {
		return $this->started;
	}

	/**
	 * Get the underlying session handler implementation.
	 *
	 * @return SessionHandlerInterface
	 */
	public function get_handler() {
		return $this->handler;
	}

	/**
	 * Get the name of the session.
	 *
	 * @return string
	 */
	public function get_name() {
	    return $this->name;
	}

	/**
	 * Set the name of the session.
	 *
	 * @param  string  $name
	 * @return void
	 */
	public function set_name( $name ) {
	    $this->name = $name;
	}

	/**
	 * Get the current session ID.
	 *
	 * @return string
	 */
	public function get_id() {
		return $this->id;
	}

	/**
	 * Set the session ID.
	 *
	 * @param  string $id
	 * @return void
	 */
	public function set_id( $id ) {
		$this->id = $this->is_valid_id( $id ) ? $id : $this->generate_session_id();
	}

	/**
	 * Determine if this is a valid session ID.
	 *
	 * @param  string $id
	 * @return bool
	 */
	public function is_valid_id( $id ) {
		return is_string( $id ) && ( function_exists( 'ctype_alnum' ) && ctype_alnum( $id ) ) && strlen( $id ) === 40;
	}

	/**
	 * Get a new, random session ID.
	 *
	 * @return string
	 */
	protected function generate_session_id() {
		return awebooking_random_string( 40 );
	}

	/**
	 * Count the number of items in the collection.
	 *
	 * @return int
	 */
	public function count() {
		return count( $this->attributes );
	}

	/**
	 * Determine if an item exists at an offset.
	 *
	 * @param  mixed  $key
	 * @return bool
	 */
	public function offsetExists( $key ) {
		return $this->exists( $key );
	}

	/**
	 * Get an item at a given offset.
	 *
	 * @param  mixed  $key
	 * @return mixed
	 */
	public function offsetGet( $key ) {
		return $this->get( $key );
	}

	/**
	 * Set the item at a given offset.
	 *
	 * @param  mixed  $key
	 * @param  mixed  $value
	 * @return void
	 */
	public function offsetSet( $key, $value ) {
		$this->put( $key, $value );
	}

	/**
	 * Unset the item at a given offset.
	 *
	 * @param  string  $key
	 * @return void
	 */
	public function offsetUnset( $key ) {
		$this->remove( $key );
	}
}
