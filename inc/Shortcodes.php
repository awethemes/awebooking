<?php
namespace AweBooking;

use AweBooking\Support\Utils;
use AweBooking\Support\Formatting;

class Shortcodes {

	/**
	 * Init shortcodes.
	 */
	public static function init() {
		$shortcodes = array(
			'awebooking_check_availability'  => __CLASS__ . '::check_availability',
			'awebooking_booking'             => __CLASS__ . '::booking',
			'awebooking_checkout'            => __CLASS__ . '::checkout',
			'awebooking_check_form'          => __CLASS__ . '::check_form',
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
			'class'  => 'awebooking',
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

		$layout = 'vertical';

		if ( '' === $atts['layout'] ) {
			$layout = 'check-availability-form.php';
		}

		$layout = apply_filters( 'awebooking/check_availability/layout', $layout, $atts );

		abkng_get_template( $layout, array( 'atts' => $atts ) );
	}
}
