<?php

/**
 * Query customers and return customer IDs.
 *
 * @param  string $term  The search term.
 * @param  int    $limit Limit the search results.
 * @return array
 */
function abrs_search_customers( $term, $limit = 0 ) {
	// Apply fillter to allow users custom the results.
	$results = apply_filters( 'abrs_pre_search_customers', false, $term, $limit );

	// If custom search results available, just return it.
	if ( is_array( $results ) ) {
		return $results;
	}

	$query = new WP_User_Query( apply_filters( 'abrs_customer_search_query', [
		'fields'         => 'ID',
		'number'         => $limit,
		'search'         => '*' . esc_attr( $term ) . '*',
		'search_columns' => [ 'user_login', 'user_url', 'user_email', 'user_nicename', 'display_name' ],
	], $term, $limit, 'main_query' ) );

	$query2 = new WP_User_Query( apply_filters( 'abrs_customer_search_query', [
		'fields'         => 'ID',
		'number'         => $limit,
		'meta_query'     => [
			'relation' => 'OR',
			[
				'key'     => 'first_name',
				'value'   => $term,
				'compare' => 'LIKE',
			],
			[
				'key'     => 'last_name',
				'value'   => $term,
				'compare' => 'LIKE',
			],
		],
	], $term, $limit, 'meta_query' ) );

	// Merge the both results.
	$results = wp_parse_id_list(
		array_merge( (array) $query->get_results(), (array) $query2->get_results() )
	);

	// Limit the results.
	if ( $limit && count( $results ) > $limit ) {
		$results = array_slice( $results, 0, $limit );
	}

	return $results;
}
