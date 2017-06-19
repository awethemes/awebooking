<?php
namespace AweBooking\Support;

use WP_Post;
use WP_Term;
use ArrayAccess;
use JsonSerializable;
use InvalidArgumentException;
use AweBooking\Interfaces\Store;
use AweBooking\Interfaces\Jsonable;
use AweBooking\Interfaces\Arrayable;

abstract class WP_Object implements ArrayAccess, Arrayable, Jsonable, JsonSerializable {
	/**
	 * ID for this object.
	 *
	 * @var int
	 */
	protected $id;

	/**
	 * WP Object (WP_Post, WP_Term, etc...) instance.
	 *
	 * @var mixed
	 */
	protected $instance;

	/**
	 * Indicates if the object exists.
	 *
	 * @var bool
	 */
	protected $exists = false;

	/**
	 * Name of object type.
	 *
	 * Normally is name of custom-post-type or custom-taxonomy.
	 *
	 * @var string
	 */
	protected $object_type = 'post';

	/**
	 * Type of object metadata is for (e.g., term, post).
	 *
	 * @var string
	 */
	protected $meta_type = 'post';

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
	 * The attributes that should be cast to native types.
	 *
	 * @var array
	 */
	protected $casts = [];

	/**
	 * An array of metadata mapped with attributes.
	 *
	 * @var array
	 */
	protected $maps = [];

	/**
	 * Additional metadata of this object.
	 *
	 * @var array
	 */
	protected $metadata;

	/**
	 * WP Object constructor.
	 *
	 * @param mixed $object Object ID we'll working for.
	 */
	public function __construct( $object = 0 ) {
		if ( is_numeric( $object ) && $object > 0 ) {
			$this->set_id( $object );
		} elseif ( 'post' === $this->meta_type && ! empty( $object->ID ) ) {
			$this->set_id( $object->ID );
		} elseif ( 'term' === $this->meta_type && ! empty( $object->term_id ) ) {
			$this->set_id( $object->term_id );
		}

		// Try get attributes via setup_attributes() method.
		if ( empty( $this->attributes ) ) {
			$this->setup_attributes();
		}

		// Setup the wp core object instance.
		$this->setup_instance();
		$this->exists = ! is_null( $this->instance );

		// If object mark exists, setup the attributes.
		if ( $this->exists ) {
			$this->setup_metadata();
			$this->setup();
		}

		// Set original to attributes so we can track and reset attributes if needed.
		$this->sync_original();
	}

	/**
	 * Setup the object attributes.
	 *
	 * @return void
	 */
	abstract protected function setup();

	/**
	 * Return an array of attributes for this object.
	 *
	 * This is (abstract) but optional method.
	 *
	 * @return void
	 */
	protected function setup_attributes() {}

	/**
	 * Get the object ID.
	 *
	 * @return integer
	 */
	public function get_id() {
		return $this->id;
	}

	/**
	 * Set object ID.
	 *
	 * @param integer $id Object ID to set.
	 */
	public function set_id( $id ) {
		$this->id = absint( $id );
	}

	/**
	 * Get an attribute from this object.
	 *
	 * @param  string $key Attribute key name.
	 * @return mixed|null
	 */
	public function get_attr( $key ) {
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
	 * @param string $key   Name of attribute to set.
	 * @param mixed  $value Value of new attribute.
	 */
	public function set_attr( $key, $value ) {
		$this->attributes[ $key ] = $value;
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
	 * Get all metadata of this object.
	 *
	 * @return array
	 */
	public function get_metadata() {
		// Fetching the meta-data for the first time.
		if ( is_null( $this->metadata ) ) {
			$this->fetch_metadata();
		}

		return $this->metadata;
	}

	/**
	 * Fetch metadata of current object.
	 *
	 * @return void
	 */
	public function fetch_metadata() {
		if ( ! $this->meta_type ) {
			$this->metadata = [];
			return;
		}

		$metadata = [];

		// Get raw metadata of this object.
		// The meta type is defined by {$this->meta_type} - 'post' by default,
		// and sometime it can be "term", dependent your object you working for.
		$raw_metadata = get_metadata( $this->meta_type, $this->get_id() );

		// Loop through raw metadata and setup object metadata.
		foreach ( $raw_metadata as $meta_key => $meta_values ) {
			if ( in_array( $meta_key, [ '_edit_lock', '_edit_last' ] ) ) {
				continue;
			}

			// AweBooking work only with single meta.
			// So just try unserialize the first value.
			$metadata[ $meta_key ] = maybe_unserialize( $meta_values[0] );
		}

		/**
		 * Allow third-party change the metadata.
		 *
		 * @param array           $metadata  Current object metadata.
		 * @param mixed|WP_Object $wp_object WP_Object instance.
		 * @var array
		 */
		$metadata = apply_filters( $this->prefix( 'metadata' ), $metadata, $this );

		// Map metadata to the object.
		$this->metadata = $metadata;
	}

	/**
	 * Determine whether a mapping exists for an attribute.
	 *
	 * @param  string $metadata Mapped key to check.
	 * @return boolean
	 */
	public function has_mapping( $metadata ) {
		return array_key_exists( $metadata, $this->get_mapping() );
	}

	/**
	 * Get the mapping array.
	 *
	 * @return array
	 */
	public function get_mapping() {
		$maps = [];

		foreach ( $this->maps as $metadata => $attribute ) {
			// Allowed using a single if same name of metadata and attribute .
			// eg: `gallery` same as: `gallery => gallery`.
			$_metadata = is_int( $metadata ) ? $attribute : $metadata;

			// Ignore the un-defined attribute.
			if ( ! array_key_exists( $attribute, $this->attributes ) ) {
				continue;
			}

			$maps[ $_metadata ] = $attribute;
		}

		return $maps;
	}

	/**
	 * Mapped metadata with the attributes.
	 *
	 * @return void
	 */
	protected function setup_metadata() {
		$metadata = $this->get_metadata();

		// Leave if no metadata for this object.
		if ( empty( $metadata ) ) {
			return;
		}

		// Loop through the $maps and do the mapping.
		foreach ( $this->get_mapping() as $metadata => $attribute ) {
			$mapped_value = $this->get_meta( $metadata );

			// Ignore if a metadata not found.
			if ( is_null( $mapped_value ) ) {
				continue;
			}

			// Cast attribute to native PHP type.
			if ( $this->has_cast( $attribute ) ) {
				$mapped_value = $this->cast_attribute( $attribute, $mapped_value );
			}

			// Finally, set the attribute value with mapped ID.
			$this->set_attr( $attribute, $mapped_value );
		}
	}

	/**
	 * Determine if the object or given attribute(s) have been modified.
	 *
	 * TODO: Need the code.
	 *
	 * @param  array|string|null $attributes Optional, a special attribute or an array of attributes.
	 * @return bool
	 */
	public function has_change( $attributes ) {
		return false;
	}

	/**
	 * Get the attributes that have been changed.
	 *
	 * TODO: Need the code.
	 *
	 * @return array
	 */
	public function get_changes() {
		return [];
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
			$results[ $attribute ] = $this->get_attr( $attribute );
		}

		return $results;
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
	 * Get the casts array.
	 *
	 * @return array
	 */
	public function get_casts() {
		return $this->casts;
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
			case 'bool':
			case 'boolean':
				return (bool) $value;
			default:
				return $value;
		}
	}

	/**
	 * Determine if object exists.
	 *
	 * @return boolean
	 */
	public function exists() {
		return $this->exists;
	}

	/**
	 * Get the object instance,
	 *
	 * @return mixed
	 */
	public function get_instance() {
		return $this->instance;
	}

	/**
	 * Set object instance with WP Core Object (WP_Post, WP_Term).
	 *
	 * @param  mixed $wp_object WP Core Object instance.
	 * @throws InvalidArgumentException
	 */
	public function set_instance( $wp_object ) {
		// Only support WP_Post and WP_Term by default.
		if ( $wp_object instanceof WP_Post || $wp_object instanceof WP_Term ) {
			$this->instance = $wp_object;
			return;
		}

		throw new InvalidArgumentException( esc_html__( 'Unsupported the instance of WP Object.', 'awebooking' ) );
	}

	/**
	 * Setup WP Core Object based on ID and object-type.
	 *
	 * @return void
	 */
	protected function setup_instance() {
		switch ( $this->meta_type ) {
			case 'post':
				$wp_post = get_post( $this->get_id() );
				if ( ! is_null( $wp_post ) && get_post_type( $wp_post->ID ) === $this->object_type ) {
					$this->set_instance( $wp_post );
				}
				break;

			case 'term':
				$wp_term = get_term( $this->get_id(), $this->object_type );
				if ( ! is_null( $wp_term ) && ! is_wp_error( $wp_term ) ) {
					$this->set_instance( $wp_term );
				}
				break;
		}
	}

	/**
	 * Prefix for action and filter hooks for this object.
	 *
	 * @param  string $hook_name Hook name without prefix.
	 * @return string
	 */
	protected function prefix( $hook_name ) {
		return sprintf( 'awebooking/%s/%s', $this->object_type, $hook_name );
	}

	/**
	 * Get terms as IDs from a taxonomy.
	 *
	 * @param  string $taxonomy Taxonomy name.
	 * @return array
	 */
	protected function get_term_ids( $taxonomy ) {
		$terms = get_the_terms( $this->get_id(), $taxonomy );

		if ( false === $terms || is_wp_error( $terms ) ) {
			return [];
		}

		return wp_list_pluck( $terms, 'term_id' );
	}

	/**
	 * Dynamically retrieve attributes on the object.
	 *
	 * @param  string $key The attribute key name.
	 * @return mixed
	 */
	public function __get( $key ) {
		return $this->get_attr( $key );
	}

	/**
	 * Dynamically set attributes on the object.
	 *
	 * @param  string $key   The attribute key name.
	 * @param  mixed  $value The attribute value.
	 * @return void
	 */
	public function __set( $key, $value ) {
		$this->set_attr( $key, $value );
	}

	/**
	 * Determine if an attribute exists on the object.
	 *
	 * @param  string $key The attribute key name.
	 * @return bool
	 */
	public function __isset( $key ) {
		return $this->offsetExists( $key );
	}

	/**
	 * Unset an attribute on the object.
	 *
	 * @param  string $key The attribute key name to remove.
	 * @return void
	 */
	public function __unset( $key ) {
		$this->offsetUnset( $key );
	}

	/**
	 * Returns the value at specified offset.
	 *
	 * @param  string $offset The offset to retrieve.
	 * @return mixed
	 */
	public function offsetGet( $offset ) {
		return $this->get_attr( $offset );
	}

	/**
	 * Assigns a value to the specified offset.
	 *
	 * @param string $offset The offset to assign the value to.
	 * @param mixed  $value  The value to set.
	 */
	public function offsetSet( $offset, $value ) {
		$this->set_attr( $offset, $value );
	}

	/**
	 * Whether or not an offset exists.
	 *
	 * @param  string $offset An offset to check for.
	 * @return boolean
	 */
	public function offsetExists( $offset ) {
		return ! is_null( $this->get_attr( $offset ) );
	}

	/**
	 * Unsets an offset.
	 *
	 * @param string $offset The offset to unset.
	 */
	public function offsetUnset( $offset ) {
		unset( $this->attributes[ $offset ] );
	}

	/**
	 * Retrieves the data for JSON serialization.
	 *
	 * @return array
	 */
	public function jsonSerialize() {
		return $this->to_array();
	}

	/**
	 * Retrieves the attributes as array.
	 *
	 * @return array
	 */
	public function to_array() {
		return array_merge( [ 'id' => $this->get_id() ], $this->attributes );
	}

	/**
	 * Convert the object to its JSON representation.
	 *
	 * @param  int $options JSON encode options.
	 * @return string
	 */
	public function to_json( $options = 0 ) {
		$json = json_encode( $this->jsonSerialize(), $options );

		if ( JSON_ERROR_NONE !== json_last_error() ) {
			// TODO: Logging or throw the error message if happend,
			// using json_last_error_msg(); function to get error message.
		}

		return $json;
	}

	/**
	 * Convert the object to its string representation.
	 *
	 * @return string
	 */
	public function __toString() {
		return $this->to_json();
	}
}
