<?php
namespace Skeleton;

use Skeleton\Support\Multidimensional;

class WP_Option {
	/**
	 * Current option key.
	 *
	 * @var string
	 */
	protected $key;

	/**
	 * Options array.
	 *
	 * @var array
	 */
	protected $options = array();

	/**
	 * Initiate option object.
	 *
	 * @param string $key Option key where data will be saved.
	 */
	public function __construct( $key ) {
		$this->key = $key;
		$this->options = (array) get_option( $this->key, array() );
	}

	/**
	 * Retrieves all options.
	 *
	 * @return array
	 */
	public function all() {
		return $this->options;
	}

	/**
	 * Check an option in current options.
	 *
	 * @param  string $key
	 * @return bool
	 */
	public function has( $key ) {
		return ! is_null( $this->get( $key ) );
	}

	/**
	 * Retrieves an option from current options.
	 *
	 * @param  string $key
	 * @param  mixed  $default
	 * @return mixed
	 */
	public function get( $key, $default = null ) {
		return Multidimensional::find( $this->options, $key, $default );
	}

	/**
	 * Set an option to current options.
	 *
	 * @param string $key
	 * @param mixed  $value
	 * @param bool   $save_after
	 */
	public function set( $key, $value, $save_after = true ) {
		Multidimensional::replace( $this->options, $key, $value );

		if ( $save_after ) {
			$this->save();
		}
	}

	/**
	 * Delete an option in current options.
	 *
	 * @param  string $key
	 * @param  bool   $save_after
	 * @return bool
	 */
	public function delete( $key, $save_after = true ) {
		$deleted = Multidimensional::delete( $this->options, $key );

		if ( $deleted && $save_after ) {
			return $this->save();
		}

		return $deleted;
	}

	/**
	 * Save current options to database.
	 *
	 * @param  array $options Optional data to save.
	 * @return boolean
	 */
	public function save( $options = array() ) {
		return update_option( $this->key, $options ? $options : $this->options );
	}
}
