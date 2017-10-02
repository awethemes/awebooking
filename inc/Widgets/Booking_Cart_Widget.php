<?php
namespace AweBooking\Widgets;

use Skeleton\Widget;

class Booking_Cart_Widget extends Widget {
	/**
	 * Array of default values for widget settings.
	 *
	 * @var array
	 */
	public $defaults = array(
		'title'         => '',
	);

	/**
	 * Contructor the widget.
	 */
	public function __construct() {
		parent::__construct(
			'awebooking_cart',
			esc_html__( 'AweBooking: Booking Cart', 'awebooking' ),
			[
				'classname'   => 'awebooking-cart-widget',
				'description' => esc_html__( 'Display cart status.', 'awebooking' ),
			]
		);
	}

	/**
	 * Front-end display of widget.
	 *
	 * @param  array $args      The widget arguments set up when a sidebar is registered.
	 * @param  array $instance  The widget settings as set by user.
	 */
	public function widget( $args, $instance ) {
		$instance = wp_parse_args( (array) $instance, $this->defaults );

		/** This filter is documented in wp-includes/widgets/class-wp-widget-pages.php */
		$title = apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base );

		// Output the widget.
		echo $args['before_widget'];
		if ( $title ) {
			echo $args['before_title'] . $title . $args['after_title'];
		}

		echo do_shortcode( '[awebooking_cart]' );
		echo $args['after_widget']; // WPCS: XSS OK.
	}

	/**
	 * Array of widget fields args.
	 *
	 * @var array
	 */
	public function fields() {
		$fields[] = [
			'id'   => 'title',
			'type' => 'text',
			'name' => esc_html__( 'Title', 'awebooking' ),
		];

		return $fields;
	}
}
