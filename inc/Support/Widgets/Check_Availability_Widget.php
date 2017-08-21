<?php
namespace AweBooking\Widgets;

use Skeleton\Widget;

class Check_Availability_Widget extends Widget {
	/**
	 * Array of default values for widget settings.
	 *
	 * @var array
	 */
	public $defaults = array(
		'title'         => '',
		'hide_location' => false,
	);

	/**
	 * Contructor the widget.
	 */
	public function __construct() {
		parent::__construct(
			'awebooking_check_availability',
			esc_html__( 'AweBooking: Check Availability', 'awebooking' ),
			[
				'classname'   => 'awebooking-check-availability-widget',
				'description' => esc_html__( 'Display availability check form.', 'awebooking' ),
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

		// Build shortcode attributes.
		$attributes = apply_filters( 'awebooking/widget/check_availability_attributes', [
			'hide_location' => ( awebooking()->is_multi_location() && $instance['hide_location'] ) ? '1' : '0',
		]);

		// Output the widget.
		echo $args['before_widget'];
		if ( $title ) {
			echo $args['before_title'] . $title . $args['after_title'];
		}

		$html_attributes = '';
		foreach ( $attributes as $key => $value ) {
			$html_attributes .= $key . '="' . esc_attr( $value ) . '" ';
		}

		do_shortcode( '[awebooking_check_form ' . $html_attributes . ']' );
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

		if ( awebooking()->is_multi_location() ) {
			$fields[] = [
				'id'   => 'hide_location',
				'type' => 'checkbox',
				'desc' => esc_html__( 'Hide Location?', 'awebooking' ),
			];
		}

		return $fields;
	}
}
