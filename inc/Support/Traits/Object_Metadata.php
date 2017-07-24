<?php
namespace AweBooking\Support\Traits;

trait Object_Metadata {
	/**
	 * Type of object metadata is for (e.g., term, post).
	 *
	 * @var string
	 */
	protected $meta_type = 'post';

	/**
	 * An array of attributes mapped with metadata.
	 *
	 * @var array
	 */
	protected $maps = [];

	/**
	 * Store additional metadata of this object.
	 *
	 * @var array
	 */
	protected $metadata;

	/**
	 * Store normalized of mapping metadata.
	 *
	 * @var array
	 */
	protected $mapping;

	/**
	 * Mapped metadata with the attributes.
	 *
	 * @return void
	 */
	protected function setup_metadata() {
		if ( ! $this->meta_type ) {
			return;
		}

		$metadata = $this->get_metadata();
		foreach ( $this->get_mapping() as $attribute => $meta ) {
			if ( isset( $metadata[ $meta ] ) ) {
				$this->set_attr( $attribute, $metadata[ $meta ] );
			}
		}
	}

	/**
	 * Get a metadata by meta key.
	 *
	 * @param  string $key The metadata key.
	 * @return mixed|null
	 */
	public function get_meta( $key ) {
		$metadata = $this->get_metadata();

		if ( ! array_key_exists( $key, $metadata ) ) {
			return;
		}

		return $metadata[ $key ];
	}

	/**
	 * Add metadata.
	 *
	 * @param string $meta_key   Metadata key.
	 * @param mixed  $meta_value Metadata value. Must be serializable if non-scalar.
	 */
	public function add_meta( $meta_key, $meta_value ) {
		return add_metadata( $this->meta_type, $this->get_id(), $meta_key, $meta_value, true );
	}

	/**
	 * Update metadata.
	 *
	 * @param  string $meta_key   Metadata key.
	 * @param  mixed  $meta_value Metadata value. Must be serializable if non-scalar.
	 * @return bool|int
	 */
	public function update_meta( $meta_key, $meta_value ) {
		return update_metadata( $this->meta_type, $this->get_id(), $meta_key, $meta_value );
	}

	/**
	 * Delete metadata.
	 *
	 * @param  string $meta_key Metadata key.
	 * @return bool
	 */
	public function delete_meta( $meta_key ) {
		return delete_metadata( $this->meta_type, $this->get_id(), $meta_key, '', true );
	}

	/**
	 * Get all metadata of this object.
	 *
	 * @return array
	 */
	public function get_metadata() {
		// Fetching the meta-data for the first time.
		if ( is_null( $this->metadata ) ) {
			$this->metadata = $this->fetch_metadata();
		}

		return $this->metadata;
	}

	/**
	 * Fetch metadata of current object.
	 *
	 * @return array
	 */
	protected function fetch_metadata() {
		// If no meta_type found, don't do anything, leave
		// and return an empty array.
		if ( ! $this->meta_type ) {
			return array();
		}

		// Get raw metadata of this object.
		// The meta type is defined by {$this->meta_type} - 'post' by default,
		// and it can be "term", dependent your object you working for.
		$raw_metadata = get_metadata( $this->meta_type, $this->get_id() );

		$metadata = [];

		// Loop through raw metadata and setup object metadata.
		foreach ( $raw_metadata as $meta_key => $meta_values ) {
			if ( in_array( $meta_key, [ '_edit_lock', '_edit_last' ] ) ) {
				continue;
			}

			// AweBooking work only with single meta.
			// So just try unserialize the first value.
			$metadata[ $meta_key ] = maybe_unserialize( $meta_values[0] );
		}

		return $metadata;
	}

	/**
	 * Determine if the object or given attribute(s) were mapped.
	 *
	 * @param  string $attributes Optional, an array or string attribute(s).
	 * @return bool
	 */
	public function has_mapping( $attributes = null ) {
		$mapping = $this->get_mapping();

		if ( is_null( $attributes ) ) {
			return count( $mapping ) > 0;
		}

		$attributes = is_array( $attributes ) ? $attributes : func_get_args();
		foreach ( $attributes as $attribute ) {
			if ( array_key_exists( $attribute, $mapping ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Get the mapping metakey by special attribute.
	 *
	 * @param  string $attribute The attribute key to get metakey.
	 * @return string|null
	 */
	public function get_mapping_metakey( $attribute ) {
		$mapping = $this->get_mapping();

		return isset( $mapping[ $attribute ] ) ? $mapping[ $attribute ] : null;
	}

	/**
	 * Get normalized of mapping.
	 *
	 * @return array
	 */
	public function get_mapping() {
		if ( is_null( $this->mapping ) ) {
			$this->mapping = $this->normalize_mapping();
		}

		return $this->mapping;
	}

	/**
	 * Normalize mapping metadata.
	 *
	 * @return array
	 */
	protected function normalize_mapping() {
		$mapping = [];

		foreach ( $this->maps as $attribute => $metadata ) {
			// Allowed using same name of attribute and metadata.
			// Eg: [gallery] same as [gallery => gallery].
			$attribute = is_int( $attribute ) ? $metadata : $attribute;

			if ( array_key_exists( $attribute, $this->attributes ) ) {
				$mapping[ $attribute ] = $metadata;
			}
		}

		return $mapping;
	}

	/**
	 * Run perform update object metadata.
	 *
	 * @param  array $changes The attributes changed.
	 * @return array|null
	 */
	protected function perform_update_metadata( array $changes ) {
		if ( ! $this->meta_type ) {
			return;
		}

		$mapping = $this->get_mapping();
		$changes = $this->get_changes_only( $changes, array_keys( $mapping ) );

		// Don't do anything if nothing changes.
		if ( empty( $changes ) ) {
			return;
		}

		$updated = [];

		foreach ( $changes as $attribute ) {
			$meta_key = $this->get_mapping_metakey( $attribute );

			if ( $meta_key && $this->update_meta( $meta_key, $this->get_attr( $attribute ) ) ) {
				$updated[] = $attribute;
			}
		}

		return $updated;
	}
}
