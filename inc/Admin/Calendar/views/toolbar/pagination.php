<?php

$page_links = paginate_links( [
	'type'      => 'list',
	'format'    => '',
	'base'      => add_query_arg( 'paged', '%#%' ),
	'prev_text' => '«',
	'next_text' => '»',
	'total'     => $calendar->pagination_args['total_items'],
	'current'   => max( 1, (int) $calendar->request->get( 'paged' ) ),
	'end_size'  => 1,
	'mid_size'  => 1,
] );

if ( $page_links ) {
	echo '<div class="scheduler__toolbar abrs-page-links">' . $page_links . '</div>'; // @codingStandardsIgnoreLine
}
