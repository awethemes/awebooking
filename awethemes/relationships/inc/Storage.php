<?php
namespace Awethemes\Relationships;

class Storage {
	/**
	 * Init the WP hooks.
	 *
	 * Call this method before the "init" hook.
	 *
	 * @return void
	 */
	public function init() {
		add_action( 'init', [ $this, 'register_tables' ], 0 );
		add_filter( 'wpmu_drop_tables', [ $this, 'wpmu_drop_tables' ] );
		add_action( 'deleted_post', [ $this, 'delete_relationship_objects' ] );
	}

	/**
	 * Perform create the tables.
	 *
	 * @return void
	 */
	public function install() {
		global $wpdb;

		if ( ! function_exists( 'dbDelta' ) ) {
			require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		}

		$collate = '';
		if ( $wpdb->has_cap( 'collation' ) ) {
			$collate = $wpdb->get_charset_collate();
		}

		dbDelta("
CREATE TABLE {$wpdb->prefix}p2p_relationships (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  type VARCHAR(42) NOT NULL DEFAULT '',
  rel_from BIGINT UNSIGNED NOT NULL,
  rel_to BIGINT UNSIGNED NOT NULL,
  PRIMARY KEY (id),
  KEY type (type),
  KEY rel_from (rel_from),
  KEY rel_to (rel_to)
) $collate;
CREATE TABLE {$wpdb->prefix}p2p_relationshipmeta (
  meta_id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  p2p_relationship_id BIGINT UNSIGNED NOT NULL,
  meta_key VARCHAR(191) default NULL,
  meta_value longtext NULL,
  PRIMARY KEY (meta_id),
  KEY p2p_relationship_id (p2p_relationship_id),
  KEY meta_key (meta_key(32))
) $collate;");
	}

	/**
	 * Perform drop the tables.
	 *
	 * @return void
	 */
	public function uninstall() {
		global $wpdb;

		// @codingStandardsIgnoreStart
		$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}p2p_relationships" );
		$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}p2p_relationshipmeta" );
		// @codingStandardsIgnoreEnd
	}

	/**
	 * Register the tables into the $wpdb.
	 *
	 * @access private
	 */
	public function register_tables() {
		global $wpdb;

		$wpdb->tables[] = 'p2p_relationships';
		$wpdb->p2p_relationships = $wpdb->prefix . 'p2p_relationships';

		$wpdb->tables[] = 'p2p_relationshipmeta';
		$wpdb->p2p_relationshipmeta = $wpdb->prefix . 'p2p_relationshipmeta';
	}

	/**
	 * Uninstall tables when MU blog is deleted.
	 *
	 * @access private
	 *
	 * @param  array $tables List the tables to be deleted.
	 * @return array
	 */
	public function wpmu_drop_tables( $tables ) {
		global $wpdb;

		$tables[] = $wpdb->prefix . 'p2p_relationships';
		$tables[] = $wpdb->prefix . 'p2p_relationshipmeta';

		return $tables;
	}

	/**
	 * Perform delete relationship objects.
	 *
	 * @return void
	 */
	public function delete_relationship_objects() {
		// TODO: ...
		$object_type = str_replace( 'deleted_', '', current_action() );
	}

	/**
	 * Add a relationship for two objects.
	 *
	 * @param string    $type      The relationship type.
	 * @param int|mixed $from      The "from" object ID.
	 * @param int|mixed $to        The "to" object ID.
	 * @param string    $direction The direction: "from" or "to".
	 *
	 * @return bool|int
	 */
	public function create( $type, $from, $to, $direction = Relationship::DIRECTION_FROM ) {
		global $wpdb;

		$dirs = array_filter(
			array_map( [ Utils::class, 'parse_object_id' ], [ $from, $to ] )
		);

		if ( count( $dirs ) !== 2 ) {
			return false;
		}

		if ( Relationship::DIRECTION_TO === $direction ) {
			$dirs = array_reverse( $dirs );
		}

		$wpdb->insert(
			$wpdb->p2p_relationships,
			[
				'type'     => $type,
				'rel_from' => $dirs[0],
				'rel_to'   => $dirs[1],
			],
			[
				'%s',
				'%d',
				'%d',
			]
		);

		return $wpdb->insert_id;
	}

	/**
	 * Delete a relationship by given IDs.
	 *
	 * @param  string|array $ids The relationship ids.
	 * @return int|bool
	 */
	public function delete( $ids ) {
		global $wpdb;

		if ( empty( $ids = wp_parse_id_list( $ids ) ) ) {
			return false;
		}

		$ids = esc_sql( implode( ',', $ids ) );

		// @codingStandardsIgnoreLine
		$deleted = $wpdb->query( "DELETE FROM `{$wpdb->p2p_relationships}` WHERE `id` IN ({$ids})" );

		// @codingStandardsIgnoreLine
		$wpdb->query( "DELETE FROM `{$wpdb->p2p_relationshipmeta}` WHERE `p2p_relationship_id` IN ({$ids})" );

		return $deleted;
	}

	/**
	 * Retrieve a single connection by ID.
	 *
	 * @param  int $id The connection ID.
	 * @return array|null
	 */
	public function get( $id ) {
		global $wpdb;

		// @codingStandardsIgnoreLine
		return $wpdb->get_row( $wpdb->prepare( "SELECT * FROM `{$wpdb->p2p_relationships}` WHERE `id` = %d LIMIT 1", $id ), ARRAY_A );
	}

	/**
	 * Returns first connection in a relationship.
	 *
	 * @param string $type The relationship name.
	 * @param array  $args The query args.
	 *
	 * @return array|null
	 */
	public function first( $type, $args = [] ) {
		global $wpdb;

		$query = $this->build_query_connections( $type, array_merge( $args, [
			'limit' => 1,
		] ) );

		// @codingStandardsIgnoreLine
		return $wpdb->get_row( $query, ARRAY_A );
	}

	/**
	 * Returns connections in a relationship.
	 *
	 * @param string $type The relationship name.
	 * @param array  $args The query args.
	 *
	 * @return array|null|object
	 */
	public function find( $type, $args = [] ) {
		global $wpdb;

		// @codingStandardsIgnoreLine
		return $wpdb->get_results( $this->build_query_connections( $type, $args ), ARRAY_A );
	}

	/**
	 * Returns number of connections in a relationship.
	 *
	 * @param string $type The relationship name.
	 * @param array  $args The query args.
	 *
	 * @return mixed
	 */
	public function count( $type, $args = [] ) {
		global $wpdb;

		$query = $this->build_query_connections( $type, array_merge( $args, [
			'column' => 'count',
		] ) );

		// @codingStandardsIgnoreLine
		return $wpdb->get_var( $query );
	}

	/**
	 * Build the query to retrieve connections.
	 *
	 * @param string $type The relationship name.
	 * @param array  $args The query args.
	 *
	 * @return string
	 */
	protected function build_query_connections( $type, $args = [] ) {
		global $wpdb;

		$args = wp_parse_args( $args, [
			'from'      => '*',
			'to'        => '*',
			'limit'     => -1,
			'column'    => 'all',
			'direction' => Relationship::DIRECTION_FROM,
		] );

		// Where clause.
		$where = $wpdb->prepare( 'WHERE `type` = %s', $type );

		// Parse the direction to using.
		$directions = Relationship::DIRECTION_ANY === $args['direction']
			? [ Relationship::DIRECTION_FROM, Relationship::DIRECTION_TO ]
			: [ $args['direction'] ];

		$rel_where = [];

		foreach ( $directions as $_direction ) {
			$_dirs = [ $args['from'], $args['to'] ];

			if ( Relationship::DIRECTION_TO === $_direction ) {
				$_dirs = array_reverse( $_dirs );
			}

			if ( $_where_caluse = $this->build_rel_where_clause( $_dirs[0], $_dirs[1] ) ) {
				$rel_where[] = '(' . $_where_caluse . ')';
			}
		}

		if ( count( $rel_where ) > 0 ) {
			$where .= ' AND ' . implode( ' OR ', $rel_where );
		}

		// Select column clause.
		switch ( $args['column'] ) {
			case 'count':
				$select = 'COUNT(*) as count';
				break;

			case '*':
			case 'all':
				$select = '*';
				break;

			default:
				$select = $args['column'];
				break;
		}

		// Limit clause.
		$limit = $args['limit'] > 0 ? "LIMIT ${args['limit']}" : '';

		return "SELECT $select FROM {$wpdb->p2p_relationships} $where $limit";
	}

	/**
	 * Build relashiship where query.
	 *
	 * @param int|array $rel_from The "from" ids.
	 * @param int|array $rel_to   The "to" ids.
	 *
	 * @return string
	 */
	protected function build_rel_where_clause( $rel_from, $rel_to ) {
		global $wpdb;

		$conditions = [];

		foreach ( compact( 'rel_from', 'rel_to' ) as $key => $value ) {
			if ( empty( $value ) || in_array( $value, [ 'any', '*' ] ) ) {
				continue;
			}

			$ids = Utils::parse_sql_ids( $value );

			if ( is_numeric( $ids ) ) {
				$conditions[] = $wpdb->prepare( "`{$key}` = %d", $ids ); // @codingStandardsIgnoreLine
			} else {
				$conditions[] = "`{$key}` IN ({$ids})";
			}
		}

		return $conditions ? implode( ' AND ', $conditions ) : '';
	}
}
