<?php
namespace AweBooking\Shortcodes;

class Shortcodes {

	/**
	 * Init shortcodes.
	 */
	public static function init() {
		$shortcodes = array(
			'awebooking_check_availability'  => __CLASS__ . '::check_availability',
			'awebooking_booking'             => __CLASS__ . '::booking',
			'awebooking_cart'                => __CLASS__ . '::cart',
			'awebooking_checkout'            => __CLASS__ . '::checkout',
			'awebooking_check_form'          => __CLASS__ . '::check_form',
			'awebooking_room_types'          => __CLASS__ . '::room_types',
		);

		foreach ( $shortcodes as $shortcode => $function ) {
			add_shortcode( apply_filters( "{$shortcode}_shortcode_tag", $shortcode ), $function );
		}
	}

	/**
	 * Shortcode Wrapper.
	 *
	 * @param string[] $function
	 * @param array $atts (default: array())
	 * @return string
	 */
	public static function shortcode_wrapper(
		$function,
		$atts    = array(),
		$wrapper = array(
			'class'  => 'awebooking-shortcode',
			'before' => null,
			'after'  => null,
		)
	) {
		ob_start();

		echo empty( $wrapper['before'] ) ? '<div class="' . esc_attr( $wrapper['class'] ) . '">' : $wrapper['before'];
		call_user_func( $function, $atts );
		echo empty( $wrapper['after'] ) ? '</div>' : $wrapper['after'];

		return ob_get_clean();
	}

	/**
	 * Check availability shortcode.
	 *
	 * @param mixed $atts
	 * @return string
	 */
	public static function check_availability( $atts ) {
		return self::shortcode_wrapper( array( 'AweBooking\\Shortcodes\\Shortcode_Check_Availability', 'output' ), $atts );
	}

	/**
	 * Check availability shortcode.
	 *
	 * @param mixed $atts
	 * @return string
	 */
	public static function booking( $atts ) {
		return self::shortcode_wrapper( array( 'AweBooking\\Shortcodes\\Shortcode_Booking', 'output' ), $atts );
	}

	/**
	 * Cart shortcode.
	 *
	 * @param mixed $atts
	 * @return string
	 */
	public static function cart( $atts ) {
		return self::shortcode_wrapper( array( 'AweBooking\\Shortcodes\\Shortcode_Cart', 'output' ), $atts );
	}

	/**
	 * Checkout page shortcode.
	 *
	 * @param mixed $atts
	 * @return string
	 */
	public static function checkout( $atts ) {
		return self::shortcode_wrapper( array( 'AweBooking\\Shortcodes\\Shortcode_Checkout', 'output' ), $atts );
	}

	/**
	 * Check form shortcode.
	 *
	 * @param mixed $atts
	 * @return string
	 */
	public static function check_form( $atts ) {
		$atts = shortcode_atts( array(
			'layout'        => '',
			'hide_location' => '',
		), $atts, 'awebooking_check_form' );

		$template = 'check-availability-form.php';

		$template = apply_filters( 'awebooking/check_availability/layout', $template, $atts );

		awebooking_get_template( $template, array( 'atts' => $atts ) );
	}

	/**
	 * Room types shortcode.
	 *
	 * @param  mixed $atts
	 * @return string
	 */
	public static function room_types( $atts ) {
		$atts = shortcode_atts( array(
			'orderby'             => 'date',
			'order'               => 'DESC',
			'posts_per_page'      => -1,
			'layout'              => '',
		), $atts, 'awebooking_room_types' );

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
