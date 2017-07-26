<?php
namespace AweBooking\Support;

use ArrayAccess;
use JsonSerializable;
use AweBooking\Interfaces\Store;
use AweBooking\Interfaces\Jsonable;
use AweBooking\Interfaces\Arrayable;

abstract class WP_Object implements ArrayAccess, Arrayable, Jsonable, JsonSerializable {
	use Traits\Object_Attributes,
		Traits\Object_Metadata;

	/**
	 * Name of object type.
	 *
	 * Normally is name of custom-post-type or custom-taxonomy.
	 *
	 * @var string
	 */
	protected $object_type = 'post';

	/**
	 * WordPress type for object, Eg: "post" and "term".
	 *
	 * @var string
	 */
	protected $wp_type = 'post';

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
	 * Indicates if the object was inserted during the current request lifecycle.
	 *
	 * @var bool
	 */
	public $recently_created = false;

	/**
	 * WP Object constructor.
	 *
	 * @param mixed $object Object ID we'll working for.
	 */
	public function __construct( $object = 0 ) {
		if ( is_numeric( $object ) && $object > 0 ) {
			$this->id = $object;
		} elseif ( 'post' === $this->meta_type && ! empty( $object->ID ) ) {
			$this->id = $object->ID;
		} elseif ( 'term' === $this->meta_type && ! empty( $object->term_id ) ) {
			$this->id = $object->term_id;
		} elseif ( $object instanceof WP_Object ) {
			$this->id = $object->get_id();
		}

		// Setup the wp core object instance.
		if ( ! is_null( $this->id ) ) {
			$this->setup_instance();
			$this->exists = ! is_null( $this->instance );

			// If object mark exists, setup the attributes.
			if ( $this->exists() ) {
				$this->setup_metadata();
				$this->setup();
			}
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
	 * Setup WP Core Object based on ID and object-type.
	 *
	 * @return void
	 */
	protected function setup_instance() {
		switch ( $this->wp_type ) {
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
	 * Update the object.
	 *
	 * @param  array $attributes The attributes to update.
	 * @return bool
	 */
	public function update( array $attributes = [] ) {
		if ( ! $this->exists() ) {
			return false;
		}

		return $this->fill( $attributes )->save();
	}

	/**
	 * Save the object to the database.
	 *
	 * @return bool
	 */
	public function save() {
		/**
		 * Fires saving action.
		 *
		 * @param static $wp_object Current object instance.
		 */
		do_action( $this->prefix( 'saving' ), $this );

		// If the object already exists we can update changes.
		// Otherwise, we'll just insert them.
		if ( $this->exists() ) {
			$saved = $this->is_dirty() ? $this->update_object() : true;
		} else {
			$saved = $this->insert_object();
		}

		if ( $saved ) {
			$this->finish_save();

			/**
			 * Fires saved action.
			 *
			 * @param static $wp_object Current object instance.
			 */
			do_action( $this->prefix( 'saved' ), $this );

			$this->sync_original();
		}

		return $saved;
	}

	/**
	 * Do somethings when finish save.
	 *
	 * @return void
	 */
	protected function finish_save() {
		$this->clean_cache();

		$this->perform_update_metadata(
			$this->recently_created ? $this->get_dirty() : $this->get_changes()
		);

		$this->resetup();
	}

	/**
	 * Clean object cache after saved.
	 *
	 * @return void
	 */
	protected function clean_cache() {}

	/**
	 * Resetup the object.
	 *
	 * @return void
	 */
	protected function resetup() {
		$this->setup_instance();

		$this->metadata = $this->fetch_metadata();
		$this->setup_metadata();

		// $this->setup();
	}

	/**
	 * Run update object.
	 *
	 * @return bool
	 */
	protected function update_object() {
		// If the "prev_create" filter returns false we'll bail out of the update and return
		// false, indicating that the save failed. This provides a chance for any
		// hooks to cancel update operations if validations fail or whatever.
		if ( false === apply_filters( $this->prefix( 'prev_create' ), true ) ) {
			return false;
		}

		/**
		 * Fires updating action.
		 *
		 * @param static $wp_object Current object instance.
		 */
		do_action( $this->prefix( 'updating' ), $this );

		$dirty = $this->get_dirty();

		if ( count( $dirty ) > 0 ) {
			$updated = $this->perform_update( $dirty );

			if ( $updated ) {
				/**
				 * Fires updated action.
				 *
				 * @param static $wp_object Current object instance.
				 */
				do_action( $this->prefix( 'updated' ), $this );

				$this->sync_changes();
			}

			return (bool) $updated;
		}

		return true;
	}

	/**
	 * Run perform update object.
	 *
	 * @see wp_update_post()
	 * @see wp_update_term()
	 * @see $this->update_the_post()
	 *
	 * @param  array $dirty The attributes has been modified.
	 * @return bool|void
	 */
	protected function perform_update( array $dirty ) {}

	/**
	 * Run insert object into database.
	 *
	 * @return bool
	 */
	protected function insert_object() {
		// If the "prev_create" filter returns false we'll bail out of the create and return
		// false, indicating that the save failed. This provides a chance for any
		// hooks to cancel create operations if validations fail or whatever.
		if ( false === apply_filters( $this->prefix( 'prev_create' ), true ) ) {
			return false;
		}

		/**
		 * Fires creating action.
		 *
		 * @param static $wp_object Current object instance.
		 */
		do_action( $this->prefix( 'creating' ), $this );

		$insert_id = $this->perform_insert();

		if ( is_int( $insert_id ) && $insert_id > 0 ) {
			// Set new ID after insert success.
			$this->id = $insert_id;

			$this->exists = true;

			$this->recently_created = true;

			/**
			 * Fires after wp-object is created.
			 *
			 * @param static $wp_object Current object instance.
			 */
			do_action( $this->prefix( 'created' ), $this );

			return true;
		}

		return false;
	}

	/**
	 * Run perform insert object into database.
	 *
	 * @see wp_insert_post()
	 * @see wp_insert_term()
	 *
	 * @return int|void
	 */
	protected function perform_insert() {}

	/**
	 * Trash or delete a wp-object.
	 *
	 * @param  bool $force Optional. Whether to bypass trash and force deletion.
	 * @return bool|null
	 */
	public function delete( $force = false ) {
		// If the object doesn't exist, there is nothing to delete
		// so we'll just return immediately and not do anything else.
		if ( ! $this->exists() ) {
			return;
		}

		// If the "prev_delete" filter returns false we'll bail out of the delete
		// and just return. Indicating that the delete failed.
		if ( false === apply_filters( $this->prefix( 'prev_delete' ), true ) ) {
			return;
		}

		/**
		 * Fires before a wp-object is deleted.
		 *
		 * @param static $wp_object Current object instance.
		 */
		do_action( $this->prefix( 'deleting' ), $this );

		$deleted = $this->perform_delete( $force );

		if ( $deleted ) {
			$this->clean_cache();

			// Now object will not exists.
			$this->exists = false;

			/**
			 * Fires after a WP_Object is deleted.
			 *
			 * @param int $object_id Object ID was deleted.
			 */
			do_action( $this->prefix( 'deleted' ), $this->get_id() );
		}

		return $deleted;
	}

	/**
	 * Perform delete object.
	 *
	 * @see wp_delete_post()
	 * @see wp_delete_term()
	 *
	 * @param  bool $force Force delete or not.
	 * @return bool
	 */
	protected function perform_delete( $force ) {
		switch ( $this->wp_type ) {
			case 'term':
				$delete = wp_delete_term( $this->get_id(), $force );
				return ( ! is_wp_error( $delete ) && true === $delete );

			case 'post':
				if ( ! $force && 'trash' !== get_post_status( $this->get_id() ) && EMPTY_TRASH_DAYS ) {
					$delete = wp_trash_post( $this->get_id() );
				} else {
					$delete = wp_delete_post( $this->get_id(), true );
				}

				return ( ! is_null( $delete ) && ! is_wp_error( $delete ) && false !== $delete );
		}

		return false;
	}

	/**
	 * Get the object ID.
	 *
	 * @return int
	 */
	public function get_id() {
		return $this->id;
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
	 * Determine if object exists.
	 *
	 * @return bool
	 */
	public function exists() {
		return $this->exists;
	}

	/**
	 * Set the object instance,
	 *
	 * @param  mixed $instance The object instance.
	 * @return mixed
	 */
	protected function set_instance( $instance ) {
		$this->instance = $instance;

		return $this;
	}

	/**
	 * Helper: Prefix for action and filter hooks for this object.
	 *
	 * @param  string $hook_name Hook name without prefix.
	 * @return string
	 */
	protected function prefix( $hook_name ) {
		return sprintf( 'awebooking/%s/%s', $this->object_type, $hook_name );
	}

	/**
	 * Helper: Get terms as IDs from a taxonomy.
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
	 * Helper: Safely update a wordpress post.
	 *
	 * When updating a post, to prevent infinite loops, use $wpdb to update data,
	 * since 'wp_update_post' spawns more calls to the save_post action.
	 *
	 * @param  array $post_data An array post data to update.
	 * @return bool|null
	 */
	protected function update_the_post( array $post_data ) {
		if ( ! $this->exists() || empty( $post_data ) ) {
			return;
		}

		if ( doing_action( 'save_post' ) ) {
			$updated = $wpdb->update( $wpdb->posts, $post_data, [ 'ID' => $this->get_id() ] );
			clean_post_cache( $this->get_id() );
		} else {
			$updated = wp_update_post( array_merge( [ 'ID' => $this->get_id() ], $post_data ) );
		}

		return ( 0 !== $updated && false !== $updated );
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
	 * @return bool
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
