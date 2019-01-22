<?php

namespace AweBooking\Core\Shortcode;

use AweBooking\Constants;

class Rooms_Shortcode extends Shortcode {
	/**
	 * Default attributes.
	 *
	 * @var array
	 */
	protected $defaults = [
		'hotel'          => 0,
		'orderby'        => '',
		'order'          => '',
		'posts_per_page' => 6,
		'offset'         => 0,
	];

	/**
	 * {@inheritdoc}
	 */
	public function output( $request ) {
		// Pairs the input atts.
		$args = [
			'post_type'      => Constants::ROOM_TYPE,
			'post_status'    => 'publish',
			'orderby'        => $this->get_atts( 'orderby' ),
			'order'          => $this->get_atts( 'order' ),
			'posts_per_page' => $this->get_atts( 'posts_per_page' ),
			'offset'         => $this->get_atts( 'offset' ),
		];

		if ( $this->get_atts( 'hotel' ) ) {
			$args['meta_query'] = [
				[
					'key'     => '_hotel_id',
					'value'   => $this->get_atts( 'hotel' ),
					'compare' => '=',
				],
			];
		}

		$query = new \WP_Query( $args );

		if ( $query->have_posts() ) {
			while ( $query->have_posts() ) : $query->the_post(); // @codingStandardsIgnoreLine
				abrs_get_template_part( 'template-parts/archive/content', apply_filters( 'abrs_archive_room_layout', '' ) );
			endwhile;
		} else {
			abrs_get_template_part( 'template-parts/archive/content', 'none' );
		}

		wp_reset_postdata();
	}
}
