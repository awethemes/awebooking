<?php

namespace AweBooking\Core\Shortcode;

use AweBooking\Constants;

class Single_Room_Shortcode extends Shortcode {
	/**
	 * Default attributes.
	 *
	 * @var array
	 */
	protected $defaults = [
		'id' => 0,
	];

	/**
	 * {@inheritdoc}
	 */
	public function output( $request ) {
		$args = [
			'post_type'      => Constants::ROOM_TYPE,
			'post_status'    => 'publish',
			'posts_per_page' => 1,
			'page_id'        => $this->get_atts( 'id' ),
		];

		$query = new \WP_Query( $args );

		if ( $query->have_posts() ) {
			while ( $query->have_posts() ) : $query->the_post(); // @codingStandardsIgnoreLine
				abrs_get_template_part( 'template-parts/single/content', apply_filters( 'abrs_single_room_layout', '' ) );
			endwhile;
		} else {
			abrs_get_template_part( 'template-parts/archive/content', 'none' );
		}

		wp_reset_postdata();
	}
}
