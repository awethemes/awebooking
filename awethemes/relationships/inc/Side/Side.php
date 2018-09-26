<?php
namespace Awethemes\Relationships\Side;

abstract class Side {
	/**
	 * The title.
	 *
	 * @var string
	 */
	protected $title;

	/**
	 * The cardinality (one or many).
	 *
	 * @var string
	 */
	protected $cardinality;

	/**
	 * The user query_vars.
	 *
	 * @var array
	 */
	protected $query_vars = [];

	/**
	 * The class name transform to.
	 *
	 * @var string
	 */
	protected $class_map = 'WP_Post';

	/**
	 * The post type (using in case object_type is post).
	 *
	 * @var string
	 */
	protected $post_type = 'post';

	/**
	 * Constructor.
	 *
	 * @param array $args The side args.
	 */
	public function __construct( $args = [] ) {
		foreach ( array_keys( get_object_vars( $this ) ) as $key ) {
			if ( isset( $args[ $key ] ) ) {
				$this->{$key} = $args[ $key ];
			}
		}
	}

	/**
	 * Gets the side object type.
	 *
	 * @return string
	 */
	abstract public function get_object_type();

	/**
	 * Perform the query (WP_Query, WP_User_Query, etc.).
	 *
	 * @param  array $query_vars The query_vars args.
	 * @return mixed
	 */
	abstract public function do_query( $query_vars );

	/**
	 * Perform resolve the side label.
	 *
	 * @return string
	 */
	abstract protected function resolve_label();

	/**
	 * Return the object ID by given object.
	 *
	 * @param  mixed $object The raw object.
	 * @return int|null
	 */
	abstract public function parse_object_id( $object );

	/**
	 * Gets the side title.
	 *
	 * @return string
	 */
	public function get_title() {
		return $this->title;
	}

	/**
	 * Gets the label name.
	 *
	 * @return string
	 */
	public function get_label() {
		return $this->resolve_label();
	}

	/**
	 * Gets the query vars.
	 *
	 * @return array
	 */
	public function get_query_vars() {
		return $this->query_vars;
	}

	/**
	 * Gets the post type name.
	 *
	 * @return string
	 */
	public function get_post_type() {
		return $this->post_type;
	}

	/**
	 * Gets the cardinality.
	 *
	 * @return string
	 */
	public function get_cardinality() {
		return $this->cardinality;
	}

	/**
	 * Sets the cardinality.
	 *
	 * @param  string $cardinality The cardinality.
	 * @return $this
	 */
	public function set_cardinality( $cardinality ) {
		$this->cardinality = 'one' === $cardinality ? 'one' : 'many';

		return $this;
	}

	/**
	 * Determines if given side is same type with the current side.
	 *
	 * @param \Awethemes\Relationships\Side\Side $side The other side instance.
	 * @return bool
	 */
	public function is_same_type( Side $side ) {
		return $this->get_object_type() === $side->get_object_type();
	}

	/**
	 * Determines if current side it's the indeterminate connection.
	 *
	 * @param \Awethemes\Relationships\Side\Side $side The other side instance.
	 * @return bool
	 */
	public function is_indeterminate( Side $side ) {
		return false;
	}

	/**
	 * Create the query.
	 *
	 * @param array $query_vars The query_vars.
	 * @return mixed
	 */
	public function query( $query_vars = [] ) {
		$query_vars = array_merge( $this->get_query_vars(), $query_vars );

		$query_vars = $this->transform_query_vars( $query_vars );

		return $this->do_query( $query_vars );
	}

	/**
	 * Transform the query_vars.
	 *
	 * @param  array $query_vars Array of query_vars.
	 * @return array
	 */
	protected function transform_query_vars( array $query_vars ) {
		return $query_vars;
	}
}
