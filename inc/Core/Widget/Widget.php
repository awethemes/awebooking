<?php

namespace AweBooking\Core\Widget;

use AweBooking\Component\Form\Form;
use AweBooking\Component\Form\Values;

abstract class Widget extends \WP_Widget {
	/**
	 * Contructor the widget.
	 *
	 * @param string $id_base         Optional Base ID for the widget, lowercase and unique. If left empty,
	 *                                a portion of the widget's class name will be used Has to be unique.
	 * @param string $name            Name for the widget displayed on the configuration page.
	 * @param array  $widget_options  Optional. Widget options. See wp_register_sidebar_widget() for information
	 *                                on accepted arguments. Default empty array.
	 * @param array  $control_options Optional. Widget control options. See wp_register_widget_control() for
	 *                                information on accepted arguments. Default empty array.
	 */
	public function __construct( $id_base, $name, $widget_options = [], $control_options = [] ) {
		parent::__construct( $id_base, $name, $widget_options, $control_options );
	}

	/**
	 * Display the widget in the front-end.
	 *
	 * @param  array $args      The widget arguments set up when a sidebar is registered.
	 * @param  array $instance  The widget settings as set by user.
	 */
	public function widget( $args, $instance ) {}

	/**
	 * Outputs the settings update form.
	 *
	 * @param array $instance Current settings.
	 */
	public function form( $instance ) {
		$controls = $this->get_controls( $instance );

		echo '<div class="cmb2-wrap awebooking-wrap"><div class="cmb2-metabox cmb2-inline-metabox">';

		foreach ( $controls->prop( 'fields' ) as $args ) {
			$controls->show_field( $args['id'] );
		}

		echo '</div></div>';
	}

	/**
	 * Update form values as they are saved.
	 *
	 * @param  array $new_instance New settings for this instance as input by the user.
	 * @param  array $old_instance Old settings for this instance.
	 * @return array
	 */
	public function update( $new_instance, $old_instance ) {
		$controls = $this->get_controls( $old_instance, true );

		return $controls->get_sanitized_values( $new_instance );
	}

	/**
	 * Gets the widget settings.
	 *
	 * @param  array $instance The instance.
	 * @return array
	 */
	protected function parse( $instance ) {
		return wp_parse_args( (array) $instance,
			array_column( (array) $this->fields(), 'default', 'id' )
		);
	}

	/**
	 * Gets the array of fields.
	 *
	 * @return array
	 */
	protected function fields() {
		return [];
	}

	/**
	 * Setup the controls.
	 *
	 * @param  \AweBooking\Component\Form\Form $controls The form controls.
	 * @return void
	 */
	protected function setup_controls( $controls ) {}

	/**
	 * Return a new instance of Form.
	 *
	 * @param  array $data   The controls data.
	 * @param  bool  $saving Is for saving?.
	 *
	 * @return \AweBooking\Component\Form\Form
	 */
	protected function get_controls( array $data, $saving = false ) {
		if ( ! $saving ) {
			$data = [ 'widget-' . $this->id_base => [ $this->number => $data ] ];
		}

		$controls = new Form( 'widget_' . $this->option_name . '_' . $this->number, new Values( $data ), 'static' );
		$this->setup_controls( $controls );

		foreach ( (array) $this->fields() as $field ) {
			if ( ! $saving ) {
				$field['id'] = $this->get_field_name( $field['id'] );
			}

			$controls->add_field( $field );
		}

		return $controls;
	}
}
