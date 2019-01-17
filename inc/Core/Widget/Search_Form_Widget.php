<?php

namespace AweBooking\Core\Widget;

class Search_Form_Widget extends Widget {
	/**
	 * Contructor.
	 */
	public function __construct() {
		parent::__construct(
			'awebooking_check_availability',
			esc_html__( 'AweBooking: Check Availability', 'awebooking' ),
			[
				'classname'   => 'awebooking-check-availability-widget',
				'description' => esc_html__( 'Display the check availability form.', 'awebooking' ),
			]
		);
	}

	/**
	 * {@inheritdoc}
	 */
	public function widget( $args, $instance ) {
		$instance = $this->parse( $instance );

		/** This filter is documented in wp-includes/widgets/class-wp-widget-pages.php */
		$title = apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base );

		// Output the widget.
		echo $args['before_widget']; // @WPCS: XSS OK.

		if ( $title ) {
			echo $args['before_title'] . $title . $args['after_title']; // @WPCS: XSS OK.
		}

		echo @do_shortcode( '[awebooking_check_form ' . abrs_html_attributes( $instance ) . ']' );

		echo $args['after_widget']; // WPCS: XSS OK.
	}

	/**
	 * {@inheritdoc}
	 */
	public function fields() {
		return [
			[
				'id'      => 'title',
				'type'    => 'text',
				'name'    => esc_html__( 'Title', 'awebooking' ),
				'default' => '',
			],
			[
				'id'      => 'layout',
				'type'    => 'select',
				'name'    => esc_html__( 'Layout', 'awebooking' ),
				'default' => 'vertical',
				'options' => [
					'vertical'   => esc_html__( 'Vertical', 'awebooking' ),
					'horizontal' => esc_html__( 'Horizontal', 'awebooking' ),
				],
			],
			[
				'id'      => 'hotel_location',
				'type'    => 'abrs_toggle',
				'name'    => esc_html__( 'Show Location?', 'awebooking' ),
				'default' => 'off',
			],
			[
				'id'              => 'container_class',
				'type'            => 'text',
				'name'            => esc_html__( 'Container class', 'awebooking' ),
				'sanitization_cb' => 'esc_attr',
			],
		];
	}
}
