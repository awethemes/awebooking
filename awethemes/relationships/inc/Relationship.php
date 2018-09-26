<?php
namespace Awethemes\Relationships;

use WP_Error;
use Awethemes\Relationships\Side\Side;

class Relationship {
	/* Constants */
	const DIRECTION_TO   = 'to';
	const DIRECTION_ANY  = 'any';
	const DIRECTION_FROM = 'from';

	const ONE_TO_ONE     = 'one-to-one';
	const ONE_TO_MANY    = 'one-to-many';
	const MANY_TO_ONE    = 'many-to-one';
	const MANY_TO_MANY   = 'many-to-many';

	/**
	 * The storage instance.
	 *
	 * @var \Awethemes\Relationships\Manager
	 */
	protected $manager;

	/**
	 * The relationship name.
	 *
	 * @var string
	 */
	protected $name;

	/**
	 * Store the direction strategy.
	 *
	 * @var \Awethemes\Relationships\Direction\Direction
	 */
	protected $strategy;

	/**
	 * The "from side" object instance.
	 *
	 * @var \Awethemes\Relationships\Side\Side
	 */
	protected $from;

	/**
	 * The "to side" object instance.
	 *
	 * @var \Awethemes\Relationships\Side\Side
	 */
	protected $to;

	/**
	 * The relationship options.
	 *
	 * @var array
	 */
	protected $options = [
		'cardinality'           => 'many-to-many',
		'reciprocal'            => false,
		'self_connections'      => false,
		'duplicate_connections' => false,
	];

	/**
	 * The valid directions.
	 *
	 * @var array
	 */
	protected static $directions = [
		self::DIRECTION_TO,
		self::DIRECTION_ANY,
		self::DIRECTION_FROM,
	];

	/**
	 * Constructor.
	 *
	 * @param \Awethemes\Relationships\Manager   $manager The manager instance.
	 * @param string                             $name    The relationship name.
	 * @param \Awethemes\Relationships\Side\Side $from    The from side.
	 * @param \Awethemes\Relationships\Side\Side $to      The to side.
	 * @param array                              $options The relationship options.
	 */
	public function __construct( Manager $manager, $name, Side $from, Side $to, $options = [] ) {
		$this->manager = $manager;

		$this->name = $name;
		$this->to   = $to;
		$this->from = $from;

		$this->options = wp_parse_args( $options, $this->options );
		$this->set_cardinality( $this->options['cardinality'] );

		$this->strategy = $manager
			->get_direction_factory()
			->create( $from, $to, $this->options['reciprocal'] );
	}

	/**
	 * //
	 *
	 * @return string
	 */
	public function get_name() {
		return $this->name;
	}

	/**
	 * Gets the storage instance.
	 *
	 * @return \Awethemes\Relationships\Storage
	 */
	public function get_storage() {
		return $this->manager->get_storage();
	}

	public function get_connected( $item ) {
		$ids = $this->get_storage()->find( $this->get_name(), [
			'from'   => $item,
			'column' => 'rel_to',
		] );

		return wp_list_pluck( $ids, 'rel_to' );
	}

	/**
	 * Returns connections in a relationship.
	 *
	 * @param array $args The query args.
	 * @return array|null|object
	 */
	public function find( $args = [] ) {
		return $this->get_storage()->find( $this->get_name(), $args );
	}

	/**
	 * Determines if two objects has any connections.
	 *
	 * @param mixed $from The from item.
	 * @param mixed $to   The to item.
	 *
	 * @return bool
	 */
	public function has( $from, $to ) {
		list( $from, $to ) = array_filter(
			func_get_args(), [ Utils::class, 'parse_object_id' ]
		);

		$count = $this->get_storage()->count( $this->get_name(), [
			'to'    => $to,
			'from'  => $from,
			'limit' => 1,
		] );

		return $count > 0;
	}

	/**
	 * Sync the intermediate tables with a list of IDs or collection of models.
	 *
	 * @param  mixed $ids
	 * @param  bool  $detaching
	 * @return array
	 */
	public function sync( $ids, $detaching = true ) {
		$changes = [
			'attached' => [],
			'detached' => [],
			'updated'  => [],
		];
	}

	/**
	 * Connect two items.
	 *
	 * @param mixed $from     The from item.
	 * @param mixed $to       The to item.
	 * @param array $metadata Optional. An array of metadata.
	 *
	 * @return int|\WP_Error
	 */
	public function connect( $from, $to, $metadata = [] ) {
		if ( ! $direction = $this->find_direction( $from ) ) {
			return new WP_Error( 'error', 'Cardinality problem (opposite).' );
		}

		// Get the directed instance.
		$directed = $this->get_direction( $direction );

		if ( ! $from = $directed->get_current()->parse_object_id( $from ) ) {
			return new WP_Error( 'first_parameter', 'Invalid first parameter.' );
		}

		if ( ! $to = $directed->get_opposite()->parse_object_id( $to ) ) {
			return new WP_Error( 'second_parameter', 'Invalid second parameter.' );
		}

		if ( $from === $to && ! $this->allow_self_connections() ) {
			return new WP_Error( 'self_connection', 'Connection between an element and itself is not allowed.' );
		}

		if ( ! $this->allow_duplicate_connections() && $this->has( $from, $to ) ) {
			return new WP_Error( 'duplicate_connection', 'Duplicate connections are not allowed.' );
		}

		/*
		if ( 'one' === $directed->get_opposite()->get_cardinality() && $this->has_connections( $from ) ) {
			return new WP_Error( 'cardinality_opposite', 'Cardinality problem (opposite).' );
		}*/

		/*if ( 'one' === $directed->get_current()->get_cardinality() ) {
			if ( $this->flip_direction()->has_connections( $to ) ) {
				return new WP_Error( 'cardinality_current', 'Cardinality problem (current).' );
			}
		}*/

		$rel_id = $this
			->get_storage()
			->create( $this->get_name(), $from, $to );

		if ( ! $rel_id ) {
			// ...
		}

		return $rel_id;
	}

	/**
	 * Disconnect two items.
	 *
	 * @param mixed $from The from item.
	 * @param mixed $to   The to item.
	 *
	 * @return bool|WP_Error Boolean or WP_Error on failure.
	 */
	public function disconnect( $from, $to ) {
		if ( ! $direction = $this->find_direction( $from ) ) {
			return new WP_Error( 'error', 'Cardinality problem (opposite).' );
		}

		// Resolve the directed.
		$directed = $this->get_direction( $direction );

		if ( ! $from = $directed->get_current()->parse_object_id( $from ) ) {
			return new WP_Error( 'first_parameter', 'Invalid first parameter.' );
		}

		if ( ! $to = $directed->get_opposite()->parse_object_id( $to ) ) {
			return new WP_Error( 'second_parameter', 'Invalid second parameter.' );
		}

		$delete = $this->get_storage()->first(
			$this->get_name(), compact( 'from', 'to' )
		);

		if ( ! is_null( $delete ) ) {
		}

		return $this->get_storage()->delete( $delete['id'] );
	}

	protected function check_objects() {
	}

	/**
	 * Gets the side instance.
	 *
	 * @param string $which Which side: to or from.
	 *
	 * @return \Awethemes\Relationships\Side\Side
	 */
	public function get_side( $which ) {
		return 'to' === $which ? $this->to : $this->from;
	}

	/**
	 * Determines if the relationship allow self connections.
	 *
	 * @return bool
	 */
	public function allow_self_connections() {
		return (bool) $this->options['self_connections'];
	}

	/**
	 * Determines if the relationship allow duplicate connections.
	 *
	 * @return bool
	 */
	public function allow_duplicate_connections() {
		return (bool) $this->options['duplicate_connections'];
	}

	/**
	 * Get relationship object type.
	 *
	 * @param string $side Only "from" or "to".
	 *
	 * @return string
	 */
	public function get_object_type( $side ) {
		return ( 'from' === $side )
			? $this->from->get_object_type()
			: $this->to->get_object_type();
	}

	/**
	 * Check if the relationship has an object type on either side.
	 *
	 * @param mixed $type The object type.
	 *
	 * @return bool
	 */
	public function has_object_type( $type ) {
		return in_array( $type, [ $this->get_object_type( 'from' ), $this->get_object_type( 'to' ) ] );
	}

	/**
	 * Find direction from given object.
	 *
	 * @param  mixed $object The object or ID.
	 * @return string|null
	 */
	public function find_direction( $object ) {
		foreach ( [ 'from', 'to' ] as $direction ) {
			if ( $object_id = $this->get_side( $direction )->parse_object_id( $object ) ) {
				return $this->strategy->choose_direction( $direction );
			}
		}
	}

	/**
	 * Resolve the direction.
	 *
	 * @param  string $direction The direction.
	 * @return \Awethemes\Relationships\Direction\Directed
	 *
	 * @throws \OutOfBoundsException
	 */
	public function get_direction( $direction = 'from' /* self::DIRECTION_FROM */ ) {
		if ( ! in_array( $direction, static::$directions ) ) {
			throw new \OutOfBoundsException( 'Invalid direction. The direction must be one of: ' . implode( ', ', static::$directions ) . '.' );
		}

		$class = $this->strategy->get_directed_class();

		return new $class( $this, $direction );
	}

	/**
	 * Get the describe string.
	 *
	 * @return string
	 */
	public function get_describe() {
		return sprintf( '%s %s %s', $this->from->get_label(), $this->strategy->get_arrow(), $this->to->get_label() );
	}

	/**
	 * Sets the cardinality on both sides.
	 *
	 * @param string $cardinality The cardinality string.
	 *
	 * @throws \InvalidArgumentException
	 */
	protected function set_cardinality( $cardinality ) {
		if ( preg_match( '/^(one|many)-to-(one|many)$/i', $cardinality, $matches ) ) {
			$this->from->set_cardinality( $matches[1] );
			$this->to->set_cardinality( $matches[2] );
			return;
		}

		throw new \InvalidArgumentException( 'Invalid cardinality' );
	}

	/**
	 * Add metadata for the specified object.
	 *
	 * @see add_metadata
	 *
	 * @param int    $object_id  ID of the object metadata is for.
	 * @param string $meta_key   Metadata key.
	 * @param mixed  $meta_value Metadata value. Must be serializable if non-scalar.
	 * @param bool   $unique     Optional, default is false.
	 * @return int|false The meta ID on success, false on failure.
	 */
	public function add_meta( $object_id, $meta_key, $meta_value, $unique = false ) {
		return add_metadata( 'p2p_relationship', $object_id, $meta_key, $meta_value, $unique );
	}

	/**
	 * Retrieve metadata for the specified object.
	 *
	 * @see get_metadata
	 *
	 * @param int    $object_id ID of the object metadata is for.
	 * @param string $meta_key  Optional. Metadata key.
	 * @param bool   $single    Optional, default is false.
	 *
	 * @return mixed
	 */
	public function get_meta( $object_id, $meta_key = '', $single = false ) {
		return get_metadata( 'p2p_relationship', $object_id, $meta_key, $single );
	}

	/**
	 * Update metadata for the specified object.
	 *
	 * If no value already exists for the specified object
	 * ID and metadata key, the metadata will be added.
	 *
	 * @see update_metadata
	 *
	 * @param int    $object_id  ID of the object metadata is for.
	 * @param string $meta_key   Metadata key.
	 * @param mixed  $meta_value Metadata value. Must be serializable if non-scalar.
	 *
	 * @return int|bool
	 */
	public function update_meta( $object_id, $meta_key, $meta_value ) {
		return update_metadata( 'p2p_relationship', $object_id, $meta_key, $meta_value );
	}

	/**
	 * Delete metadata for the specified object.
	 *
	 * @see delete_metadata
	 *
	 * @param int    $object_id  ID of the object metadata is for.
	 * @param string $meta_key   Metadata key.
	 * @param mixed  $meta_value Optional. Metadata value.
	 * @param bool   $delete_all Optional, default is false.
	 *
	 * @return bool
	 */
	public function delete_meta( $object_id, $meta_key, $meta_value = '', $delete_all = false ) {
		return delete_metadata( 'p2p_relationship', $object_id, $meta_key, $meta_value, $delete_all );
	}
}
