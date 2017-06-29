<?php
namespace Skeleton\Support;

class Multidimensional {
	/**
	 * Return item in array with multidimensional support.
	 *
	 * @param  array  $array
	 * @param  string $key
	 * @param  mixed  $default
	 * @return mixed
	 */
	public static function find( $array, $key, $default = null ) {
		if ( isset( $array[ $key ] ) ) {
			return $array[ $key ];
		}

		$id_data = static::split( $key );
		if ( empty( $id_data['keys'] ) || ! isset( $array[ $id_data['base'] ] ) ) {
			return $default;
		}

		return static::multidimensional_get( $array[ $id_data['base'] ], $id_data['keys'], $default );
	}

	/**
	 * Will attempt to replace a specific value in a multidimensional array.
	 *
	 * @param  array  $array
	 * @param  string $key
	 * @param  mixed  $value
	 * @return void
	 */
	public static function replace( &$array, $key, $value ) {
		$id_data = static::split( $key );

		$root = isset( $array[ $id_data['base'] ] ) ? $array[ $id_data['base'] ] : array();
		$array[ $id_data['base'] ] = static::multidimensional_replace( $root, $id_data['keys'], $value );
	}

	/**
	 * Will attempt to delete a specific value in a multidimensional array.
	 *
	 * @param  array  $array
	 * @param  string $key
	 * @return boolean
	 */
	public static function delete( &$array, $key ) {
		if ( is_null( static::find( $array, $key ) ) ) {
			return false;
		}

		if ( isset( $array[ $key ] ) ) {
			unset( $array[ $key ] );
			return true;
		}

		// Try multi-dimensional delete.
		$id_data = static::split( $key );
		$result = static::multidimensional( $array[ $id_data['base'] ], $id_data['keys'] );

		if ( isset( $result ) ) {
			unset( $result['node'][ $result['key'] ] );
			return true;
		}

		return false;
	}

	/**
	 * Get split id_data for multidimensional.
	 *
	 * @param  string $id
	 * @return array
	 */
	public static function split( $id ) {
		$id_data['keys'] = preg_split( '/\.|\[/', str_replace( ']', '', $id ) );
		$id_data['base'] = array_shift( $id_data['keys'] );

		return $id_data;
	}

	/**
	 * Rebuild id_data to string.
	 *
	 * @param  array $id_data
	 * @return string
	 */
	public static function join( array $id_data ) {
		$id = $id_data['base'];

		if ( ! empty( $id_data['keys'] ) ) {
			$id .= '[' . implode( '][', $id_data['keys'] ) . ']';
		}

		return $id;
	}

	/**
	 * Will attempt to fetch a specific value from a multidimensional array.
	 *
	 * @param  array $root
	 * @param  array $keys
	 * @param  mixed $default
	 * @return mixed
	 */
	public static function multidimensional_get( $root, $keys, $default = null ) {
		if ( empty( $keys ) ) { // If there are no keys, test the root.
			return isset( $root ) ? $root : $default;
		}

		$result = static::multidimensional( $root, $keys );
		return isset( $result ) ? $result['node'][ $result['key'] ] : $default;
	}

	/**
	 * Will attempt to check if a specific value in a multidimensional array is set.
	 *
	 * @param  array $root
	 * @param  array $keys
	 * @return bool
	 */
	public static function multidimensional_isset( $root, $keys ) {
		$result = static::get( $root, $keys );
		return isset( $result );
	}

	/**
	 * Will attempt to replace a specific value in a multidimensional array.
	 *
	 * @param  array $root
	 * @param  array $keys
	 * @param  mixed $value
	 * @return mixed
	 */
	public static function multidimensional_replace( $root, $keys, $value ) {
		if ( ! isset( $value ) ) {
			return $root;
		} elseif ( empty( $keys ) ) { // If there are no keys, we're replacing the root.
			return $value;
		}

		$result = static::multidimensional( $root, $keys, true );

		if ( isset( $result ) ) {
			$result['node'][ $result['key'] ] = $value;
		}

		return $root;
	}

	/**
	 * Multidimensional helper function.
	 *
	 * @param array $root
	 * @param array $keys
	 * @param bool  $create
	 * @return array|void
	 */
	protected static function multidimensional( &$root, $keys, $create = false ) {
		if ( $create && empty( $root ) ) {
			$root = array();
		}

		if ( ! isset( $root ) || empty( $keys ) ) {
			return;
		}

		$last = array_pop( $keys );
		$node = &$root;

		foreach ( $keys as $key ) {
			if ( $create && ! isset( $node[ $key ] ) ) {
				$node[ $key ] = array();
			}

			if ( ! is_array( $node ) || ! isset( $node[ $key ] ) ) {
				return;
			}

			$node = &$node[ $key ];
		}

		if ( $create ) {
			if ( ! is_array( $node ) ) {
				// Account for an array overriding a string or object value.
				$node = array();
			}

			if ( ! isset( $node[ $last ] ) ) {
				$node[ $last ] = array();
			}
		}

		if ( ! isset( $node[ $last ] ) ) {
			return;
		}

		return array(
			'root' => &$root,
			'node' => &$node,
			'key'  => $last,
		);
	}
}
