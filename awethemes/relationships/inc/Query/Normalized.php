<?php
namespace Awethemes\Relationships\Query;

use Awethemes\Relationships\Relationship;

class Normalized {
	/**
	 * The relationship instance.
	 *
	 * @var \Awethemes\Relationships\Relationship
	 */
	protected $relation;

	/**
	 * The direction.
	 *
	 * @var string
	 */
	protected $direction;

	/**
	 * The item(s).
	 *
	 * @var array|int
	 */
	protected $items;

	/**
	 * Constructor.
	 *
	 * @param \Awethemes\Relationships\Relationship $relation  The relationship instance.
	 * @param string                                $direction The direction.
	 * @param int|array                             $items     The items.
	 */
	public function __construct( Relationship $relation, $direction, $items ) {
		$this->relation  = $relation;
		$this->direction = $direction;
		$this->items     = $items;
	}

	public function get_name() {
		return $this->relation->get_name();
	}

	/**
	 * Gets the relationship instance.
	 *
	 * @return \Awethemes\Relationships\Relationship
	 */
	public function get_relation() {
		return $this->relation;
	}

	/**
	 * Gets the direction.
	 *
	 * @return string
	 */
	public function get_direction() {
		return $this->direction;
	}

	public function get_directed() {
		return $this->relation->get_direction( $this->direction );
	}

	/**
	 * Gets the items.
	 *
	 * @return array|int
	 */
	public function get_items() {
		return $this->items;
	}

	/**
	 * Perform the query.
	 *
	 * @return \Awethemes\Relationships\Query\Query
	 */
	public function get_query() {
		return new Query( $this );
	}
}
