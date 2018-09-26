<?php
namespace Awethemes\Relationships\Query;

class Post_Query {
	/**
	 * The query normalizer.
	 *
	 * @var \Awethemes\Relationships\Query\Normalizer
	 */
	protected $normalizer;

	/**
	 * Constructor.
	 *
	 * @param \Awethemes\Relationships\Query\Normalizer $normalizer The query normalizer.
	 */
	public function __construct( Normalizer $normalizer ) {
		$this->normalizer = $normalizer;
	}

	/**
	 * Filter the WordPress query to get connected posts.
	 *
	 * @return void
	 */
	public function init() {
		add_action( 'parse_query', [ $this, 'parse_query' ], 20 );
		add_filter( 'posts_clauses', [ $this, 'posts_clauses' ], 20, 2 );
	}

	/**
	 * Parse query variables.
	 *
	 * Fires after the main query vars have been parsed.
	 *
	 * @param \WP_Query $query The WP_Query instance (passed by reference).
	 */
	public function parse_query( $query ) {
		if ( ! $args = $query->get( 'relationship' ) ) {
			unset( $query->query_vars['relationship'] );
			return;
		}

		if ( ! $normalized = $this->normalizer->normalize( $args ) ) {
			unset( $query->query_vars['relationship'] );
			return;
		}

		$directed = $normalized->get_directed();

		foreach ( $directed->get_opposite()->get_query_vars() as $key => $value ) {
			$query->set( $key, $value );
		}

		$query->set( 'relationship', $normalized );
		$query->set( 'ignore_sticky_posts', true );
		$query->set( 'suppress_filters', false );

		$query->is_home    = false;
		$query->is_archive = true;
	}

	/**
	 * Filters all query clauses at once, for convenience.
	 *
	 * @param array     $clauses The list of clauses for the query.
	 * @param \WP_Query $query   The WP_Query instance (passed by reference).
	 *
	 * @return array
	 */
	public function posts_clauses( $clauses, $query ) {
		global $wpdb;

		if ( ! $query->get( 'relationship', null ) ) {
			return $clauses;
		}

		return $query->get( 'relationship' )
			->get_query()
			->alter_clauses( $clauses, "$wpdb->posts.ID" );
	}
}
