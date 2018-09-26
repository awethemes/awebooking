<?php
namespace Awethemes\Relationships;

use Awethemes\Relationships\Query\Normalizer;
use Awethemes\Relationships\Query\Post_Query;
use Awethemes\Relationships\Side\Factory as SideFactory;
use Awethemes\Relationships\Direction\Factory as DirectionFactory;

class Manager {
	/**
	 * The storage instance.
	 *
	 * @var \Awethemes\Relationships\Storage
	 */
	protected $storage;

	/**
	 * The side factory.
	 *
	 * @var \Awethemes\Relationships\Side\Factory
	 */
	protected $side_factory;

	/**
	 * The direction factory.
	 *
	 * @var \Awethemes\Relationships\Direction\Factory
	 */
	protected $direction_factory;

	/**
	 * The normalizer class name.
	 *
	 * @var string
	 */
	protected $normalizer;

	/**
	 * Storing all relationships instance.
	 *
	 * @var array
	 */
	protected $relationships = [];

	/**
	 * Indicator if the class was initialized.
	 *
	 * @var bool
	 */
	protected $initialized = false;

	/**
	 * Store the class instance.
	 *
	 * @var static
	 */
	protected static $instance;

	/**
	 * Get the class instance.
	 *
	 * @return static
	 */
	public static function get_instance() {
		if ( ! static::$instance ) {
			static::$instance = new static;
		}

		return self::$instance;
	}

	/**
	 * Constructor.
	 *
	 * @param \Awethemes\Relationships\Storage                $storage           The storage.
	 * @param \Awethemes\Relationships\Side\Factory|null      $side_factory      The side factory.
	 * @param \Awethemes\Relationships\Direction\Factory|null $direction_factory The direction factory.
	 * @param string|null                                     $normalizer        The normalizer class name.
	 */
	public function __construct(
		Storage $storage = null,
		SideFactory $side_factory = null,
		DirectionFactory $direction_factory = null,
		$normalizer = null
	) {
		if ( static::$instance ) {
			throw new \RuntimeException( 'Use Manager::get_instance() instead!' );
		}

		$this->storage           = $storage ?: new Storage;
		$this->side_factory      = $side_factory ?: new SideFactory;
		$this->direction_factory = $direction_factory ?: new DirectionFactory;
		$this->normalizer        = $normalizer ?: Normalizer::class;

		static::$instance = $this;
	}

	/**
	 * Gets the storage instance.
	 *
	 * @return \Awethemes\Relationships\Storage
	 */
	public function get_storage() {
		return $this->storage;
	}

	/**
	 * Gets the side factory.
	 *
	 * @return \Awethemes\Relationships\Side\Factory
	 */
	public function get_side_factory() {
		return $this->side_factory;
	}

	/**
	 * Gets the direction factory.
	 *
	 * @return \Awethemes\Relationships\Direction\Factory
	 */
	public function get_direction_factory() {
		return $this->direction_factory;
	}

	/**
	 * Init the relationships.
	 *
	 * @return void
	 */
	public function init() {
		if ( $this->initialized ) {
			return;
		}

		if ( false !== current_action() && did_action( 'init' ) ) {
			trigger_error( 'Initialize the relationships should not be call before the \'init\' hook.', E_USER_WARNING );
			return;
		}

		// Init the storage.
		$this->storage->init();

		// Init the queries.
		$normalizer = $this->normalizer;
		$normalizer = new $normalizer( $this );

		( new Post_Query( $normalizer ) )->init();

		$this->initialized = true;
	}

	/**
	 * Get a relationship object.
	 *
	 * @param string $name The relationship name.
	 *
	 * @return \Awethemes\Relationships\Relationship|null
	 */
	public function get( $name ) {
		return isset( $this->relationships[ $name ] ) ? $this->relationships[ $name ] : null;
	}

	/**
	 * Filter relationships by given object type.
	 *
	 * @param  string $object_type The object type.
	 *
	 * @return array
	 */
	public function filter( $object_type ) {
		return array_filter( $this->relationships, function ( Relationship $relationship ) use ( $object_type ) {
			return $relationship->has_object_type( $object_type );
		} );
	}

	/**
	 * Register a relationship.
	 *
	 * @param string       $name    The relationship name.
	 * @param string|array $from    The "from" side.
	 * @param string|array $to      The "to" side.
	 * @param array        $options The options.
	 *
	 * @return \Awethemes\Relationships\Relationship
	 */
	public function register( $name, $from, $to, $options = [] ) {
		$side_from = $this->create_side( $from, Relationship::DIRECTION_FROM );
		$side_to   = $this->create_side( $to, Relationship::DIRECTION_TO );

		$relationship = new Relationship( $this, $name, $side_from, $side_to, $options );

		return $this->relationships[ $name ] = $relationship;
	}

	/**
	 * Create the side object for a "from" or "to" side.
	 *
	 * @param array|string $args      Array of settings or post type (string) for short.
	 * @param string       $direction The relationship direction.
	 *
	 * @return \Awethemes\Relationships\Side\Side
	 */
	protected function create_side( $args, $direction ) {
		$args = $this->parse_side_args( $args, $direction );

		return $this->side_factory->create( $args['object_type'], $args );
	}

	/**
	 * Normalize side args for a "from" or "to" side.
	 *
	 * @param array|string $args      Array of settings or post type (string) for short.
	 * @param string       $direction The relationship direction.
	 *
	 * @return array
	 */
	protected function parse_side_args( $args, $direction ) {
		if ( is_string( $args ) ) {
			$object_type = $post_type = $args;

			if ( ! in_array( $object_type, [ 'user', 'term', 'attachment' ] ) ) {
				$object_type = 'post';
			}

			$args = compact( 'object_type', 'post_type' );
		}

		return wp_parse_args( $args, [
			'title'       => 'from' === $direction ? 'Connects To' : 'Connected From',
			'object_type' => 'post',
			'post_type'   => 'post',
			'query_vars'  => [],
		] );
	}
}
