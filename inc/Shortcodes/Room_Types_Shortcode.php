<?php
namespace AweBooking\Shortcodes;

use Awethemes\Http\Request;

class Room_Types_Shortcode extends Shortcode {
	/**
	 * Default shortcode attributes.
	 *
	 * @var array
	 */
	protected $default_atts = [
		'orderby'        => 'date',
		'order'          => 'DESC',
		'posts_per_page' => -1,
		'layout'         => '',
	];

	/**
	 * {@inheritdoc}
	 */
	public function output( Request $request ) {
		$query_args = apply_filters( 'awebooking/room_types_shortcode_args', [
			'post_type'           => 'room_type',
			'post_status'         => 'publish',
			'ignore_sticky_posts' => 1,
			'orderby'             => $atts['orderby'],
			'order'               => $atts['order'],
			'posts_per_page'      => $atts['posts_per_page'],
		] );

		$room_types = new \WP_Query( $query_args );

		do_action( 'awebooking/before_room_types_shortcode', $atts );

		if ( $room_types->have_posts() ) : ?>
			<ul class="room_types">
				<?php while ( $room_types->have_posts() ) : $room_types->the_post();

					awebooking_get_template_part( 'content', apply_filters( 'awebooking/room_types_shortcode_content', 'room-type', $atts ) );

				endwhile; ?>
			</ul>
		<?php endif;

		do_action( 'awebooking/after_room_types_shortcode', $atts );
		wp_reset_postdata();
	}
}
