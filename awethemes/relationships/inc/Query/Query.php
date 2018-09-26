<?php
namespace Awethemes\Relationships\Query;

use Awethemes\Relationships\Relationship;

class Query {
	/**
	 * The relationship query variables.
	 *
	 * @var \Awethemes\Relationships\Query\Normalized
	 */
	protected $attributes;

	/**
	 * Constructor.
	 *
	 * @param \Awethemes\Relationships\Query\Normalized $attributes Relationship query attributes.
	 */
	public function __construct( Normalized $attributes ) {
		$this->attributes = $attributes;
	}

	/**
	 * Modify the WordPress query to get connected object.
	 *
	 * @param array  $clauses   Query clauses.
	 * @param string $id_column Database column for object ID.
	 *
	 * @return array
	 */
	public function alter_clauses( &$clauses, $id_column ) {
		global $wpdb;

		$storage  = $this->attributes->get_relation()->get_storage();
		$items = (array) $this->attributes->get_items();

		$direction = Relationship::DIRECTION_FROM === $this->attributes->get_direction() ? 'rel_from' : 'rel_to';
		$connected = Relationship::DIRECTION_FROM === $this->attributes->get_direction() ? 'rel_to' : 'rel_from';

		$fields             = "rel.$direction AS `rel_origin`";
		$clauses['fields'] .= empty( $clauses['fields'] ) ? $fields : " , $fields";
		$clauses['join']   .= " INNER JOIN `{$wpdb->p2p_relationships}` AS rel ON rel.$connected = $id_column";
		$clauses['orderby'] = 't.term_id' === $id_column ? 'ORDER BY rel.id' : 'rel.id';

		$where = sprintf(
			"rel.type = %s AND rel.$direction IN (%s)",
			$wpdb->prepare( '%s', $this->attributes->get_name() ),
			is_array( $items ) ? implode( ',', $items ) : $items
		);

		$clauses['where'] .= empty( $clauses['where'] ) ? $where : " AND $where";

		return $clauses;
	}
}
