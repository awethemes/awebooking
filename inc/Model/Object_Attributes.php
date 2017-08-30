<?php
namespace AweBooking\Model;

trait Object_Attributes {
	/**
	 * The attributes for this object.
	 *
	 * Name value pairs (name + default value).
	 *
	 * @var array
	 */
	protected $attributes = [];

	/**
	 * The object attributes original state.
	 *
	 * @var array
	 */
	protected $original = [];

	/**
	 * The changed object attributes.
	 *
	 * @var array
	 */
	protected $changes = [];

	/**
	 * The attributes that should be cast to native types.
	 *
	 * @var array
	 */
	protected $casts = [];

	/**
	 * Get an attribute from this object.
	 *
	 * @param  string $key Attribute key name.
	 * @return mixed|null
	 */
	public function get_attribute( $key ) {
		if ( 'id' === $key ) {
			return $this->get_id();
		}

		// Return a "null" if not found attribute.
		if ( ! array_key_exists( $key, $this->attributes ) ) {
			return;
		}

		// The value should be returned.
		$value = $this->attributes[ $key ];

		// Cast to native PHP type and return if has case.
		if ( $this->has_cast( $key ) ) {
			return $this->cast_attribute( $key, $value );
		}

		return $value;
	}

	/**
	 * Sets an attribute to new value.
	 *
	 * @param  string $key   Name of attribute to set.
	 * @param  mixed  $value Value of new attribute.
	 * @return $this
	 */
	public function set_attribute( $key, $value ) {
		$method = 'set_' . str_replace( '-', '_', $key );

		// First, we'll check present of `set_$key()` method,
		// if available just call that method.
		if ( method_exists( $this, $method ) ) {
			return $this->{$method}( $value );
		}

		// If not just set attribute by normally.
		$this->attributes[ $key ] = $value;

		return $this;
	}

	/**
	 * Get all of the current attributes on the object.
	 *
	 * @return array
	 */
	public function get_attributes() {
		return $this->attributes;
	}

	/**
	 * Get a subset of the object attributes.
	 *
	 * @param  array|mixed $attributes The attributes to get.
	 * @return array
	 */
	public function only( $attributes ) {
		$results = [];

		foreach ( is_array( $attributes ) ? $attributes : func_get_args() as $attribute ) {
			$results[ $attribute ] = $this->get_attribute( $attribute );
		}

		return $results;
	}

	/**
	 * Fill the object with an array of attributes.
	 *
	 * @param  array $attributes An array of attributes to fill.
	 * @return $this
	 */
	public function fill( array $attributes ) {
		foreach ( $attributes as $key => $value ) {
			if ( ! isset( $this->attributes[ $key ] ) ) {
				continue;
			}

			$this->set_attribute( $key, $value );
		}

		return $this;
	}

	/**
	 * Set the array of object attributes.
	 *
	 * @param  array $attributes The object attributes.
	 * @param  bool  $sync       Sync original after.
	 * @return $this
	 */
	public function set_raw_attributes( array $attributes, $sync = false ) {
		$this->attributes = $attributes;

		if ( $sync ) {
			$this->sync_original();
		}

		return $this;
	}

	/**
	 * Sync the original attributes with the current.
	 *
	 * @return $this
	 */
	public function sync_original() {
		$this->original = $this->attributes;

		return $this;
	}

	/**
	 * Sync a single original attribute with its current value.
	 *
	 * @param  string $attribute The attribute to sync.
	 * @return $this
	 */
	public function sync_original_attribute( $attribute ) {
		if ( array_key_exists( $attributes, $this->attributes ) ) {
			$this->original[ $attribute ] = $this->attributes[ $attribute ];
		}

		return $this;
	}

	/**
	 * Revert a attribute to the original value.
	 *
	 * @param  string $attribute The attribute to revert.
	 * @return $this
	 */
	public function revert_attribute( $attribute ) {
		if ( array_key_exists( $attributes, $this->attributes ) ) {
			$this->attributes[ $attribute ] = $this->original[ $attribute ];
		}

		return $this;
	}

	/**
	 * Sync the changed attributes.
	 *
	 * @return $this
	 */
	public function sync_changes() {
		$this->changes = $this->get_dirty();

		return $this;
	}

	/**
	 * Determine if the object or given attribute(s) have been modified.
	 *
	 * @param  array|string|null $attributes Optional, the attribute(s) for determine.
	 * @return bool
	 */
	public function is_dirty( $attributes = null ) {
		return $this->has_changes(
			$this->get_dirty(), is_array( $attributes ) ? $attributes : func_get_args()
		);
	}

	/**
	 * Determine if the object or given attribute(s) have remained the same.
	 *
	 * @param  array|string|null $attributes Optional, the attribute(s) for determine.
	 * @return bool
	 */
	public function is_clean( $attributes = null ) {
		return ! $this->is_dirty(
			is_array( $attributes ) ? $attributes : func_get_args()
		);
	}

	/**
	 * Determine if the object or given attribute(s) have been changed.
	 *
	 * @param  array|string|null $attributes Optional, the attribute(s) for determine.
	 * @return bool
	 */
	public function was_changed( $attributes = null ) {
		return $this->has_changes(
			$this->get_changes(), is_array( $attributes ) ? $attributes : func_get_args()
		);
	}

	/**
	 * Determine if the given attributes were changed.
	 *
	 * @param  array             $changes    An array attributes was change.
	 * @param  array|string|null $attributes Optional, the attribute(s) for determine.
	 * @return bool
	 */
	protected function has_changes( array $changes, $attributes = null ) {
		if ( empty( $attributes ) ) {
			return count( $changes ) > 0;
		}

		foreach ( (array) $attributes as $attribute ) {
			if ( array_key_exists( $attribute, $changes ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Returns the attributes were changed but only in scope of $changes.
	 *
	 * @param  array        $changes    Scope of attributes changes.
	 * @param  string|array $attributes The attributes.
	 * @return array
	 */
	protected function get_changes_only( array $changes, $attributes ) {
		return array_intersect( (array) $attributes, array_keys( $changes ) );
	}

	/**
	 * Get the attributes that have been changed since last sync.
	 *
	 * @return array
	 */
	public function get_dirty() {
		$dirty = [];

		foreach ( $this->get_attributes() as $key => $value ) {
			if ( ! $this->original_is_equivalent( $key, $value ) ) {
				$dirty[ $key ] = $value;
			}
		}

		return $dirty;
	}

	/**
	 * Get the attributes that was changed.
	 *
	 * @return array
	 */
	public function get_changes() {
		return $this->changes;
	}

	/**
	 * Determine if the new and old values for a given key are equivalent.
	 *
	 * @param  string $key     The attribute key name.
	 * @param  mixed  $current Current attribute value.
	 * @return bool
	 */
	protected function original_is_equivalent( $key, $current ) {
		if ( ! array_key_exists( $key, $this->original ) ) {
			return false;
		}

		$original = $this->original[ $key ];

		if ( $current === $original ) {
			return true;
		} elseif ( is_null( $current ) ) {
			return false;
		} elseif ( $this->has_cast( $key ) ) {
			return $this->cast_attribute( $key, $current ) === $this->cast_attribute( $key, $original );
		}

		// Binary safe string comparison for numberic attribute.
		return is_numeric( $current ) && is_numeric( $original ) &&
			strcmp( (string) $current, (string) $original ) === 0;
	}

	/**
	 * Cast an attribute to a native PHP type.
	 *
	 * @param  string $key   A string of attribute key.
	 * @param  mixed  $value The raw attribute value.
	 * @return mixed
	 */
	protected function cast_attribute( $key, $value ) {
		if ( is_null( $value ) ) {
			return $value;
		}

		switch ( $this->get_cast_type( $key ) ) {
			case 'int':
			case 'integer':
				return (int) $value;
			case 'real':
			case 'float':
			case 'double':
				return (float) $value;
			case 'string':
				return (string) $value;
			case 'array':
				return (array) $value;
			case 'bool':
			case 'boolean':
				return (bool) $value;
			default:
				return $value;
		}
	}

	/**
	 * Determine whether an attribute should be cast to a native type.
	 *
	 * @param  string            $key   A string of attribute key.
	 * @param  array|string|null $types Optional, list of possible types.
	 * @return bool
	 */
	public function has_cast( $key, $types = null ) {
		if ( array_key_exists( $key, $this->get_casts() ) ) {
			return $types ? in_array( $this->get_cast_type( $key ), (array) $types, true ) : true;
		}

		return false;
	}

	/**
	 * Get the type of cast for a attribute.
	 *
	 * @param  string $key A string of attribute key.
	 * @return string
	 */
	protected function get_cast_type( $key ) {
		$casts = $this->get_casts();

		return trim( strtolower( $casts[ $key ] ) );
	}

	/**
	 * Get the casts array.
	 *
	 * @return array
	 */
	public function get_casts() {
		return $this->casts;
	}

	/**
	 * Santize attribute value before save.
	 *
	 * @param  string $key   Attribute key name.
	 * @param  mixed  $value Attribute value.
	 * @return mixed
	 */
	protected function sanitize_attribute( $key, $value ) {
		return $value;
	}
}
