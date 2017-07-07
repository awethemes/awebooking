<?php
namespace Skeleton;

use WP_Widget;
use Skeleton\CMB2\CMB2;

abstract class Widget extends WP_Widget {
	/**
	 * Array of default values for widget settings.
	 *
	 * @var array
	 */
	public $defaults = array();

	/**
	 * Store the instance properties.
	 *
	 * @var array
	 */
	protected $_instance = array();

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
	public function __construct( $id_base, $name, $widget_options = array(), $control_options = array() ) {
		parent::__construct( $id_base, $name, $widget_options, $control_options );
	}

	/**
	 * Array of widget fields args.
	 *
	 * @var array
	 */
	public function fields() {}

	/**
	 * Setup the CMB2 after created.
	 *
	 * @param  CMB2 $cmb2 CMB2 instance.
	 * @return void
	 */
	public function setup_cmb2( $cmb2 ) {}

	/**
	 * Front-end display of widget.
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
		// If there are no settings, set up defaults.
		$this->_instance = wp_parse_args( (array) $instance, (array) $this->defaults );

		$this->get_cmb2()->show_form();
	}

	/**
	 * Update form values as they are saved.
	 *
	 * @param  array $new_instance New settings for this instance as input by the user.
	 * @param  array $old_instance Old settings for this instance.
	 * @return array
	 */
	public function update( $new_instance, $old_instance ) {
		$cmb2 = $this->get_cmb2( true );
		$sanitized = $cmb2->get_sanitized_values( $new_instance );

		// Get the validation errors.
		$cmb2_errors = $cmb2->get_errors();

		// If any field not pass validation, it will be remove from $sanitized,
		// so we need add old value in to the return to avoid widget remove that field too.
		foreach ( $old_instance as $key => $value ) {
			if ( ! isset( $sanitized[ $key ] ) && isset( $cmb2_errors[ $key ] ) ) {
				$sanitized[ $key ] = $value;
			}
		}

		return $sanitized;
	}

	/**
	 * Return a new instance of CMB2.
	 *
	 * @param  bool $saving //.
	 * @return CMB2
	 */
	public function get_cmb2( $saving = false ) {
		$cmb2 = new CMB2( array(
			'id'           => 'widget_' . $this->option_name . '_' . $this->number,
			'widget'       => true,
			'hookup'       => false,
			'show_on'      => array(
				'key'   => 'options-page', // Tells CMB2 to handle this as an option.
				'value' => array( $this->option_name ),
			),
		));

		$cmb2->object_id( '_' );
		$cmb2->object_type( 'options-page' );
		$cmb2->get_render()->navigation_class = 'wp-clearfix cmb2-nav-default';

		// Setup CMB2 for the Widget.
		$this->setup_cmb2( $cmb2 );

		// Register fields into the CMB2.
		foreach ( (array) $this->fields() as $field ) {
			// Set fields ID with widget ID style if CMB2 show on widget update form.
			if ( ! $saving ) {
				$field['_original_id'] = $field['id'];
				$field['id'] = $this->get_field_name( $field['id'] );
			}

			$field['default']    = null;
			$field['default_cb'] = array( $this, '_default_field_cb' );

			$cmb2->add_field( $field );
		}

		return $cmb2;
	}

	/**
	 * Sets the field default, or the field value.
	 *
	 * @access private
	 *
	 * @param  array      $field_args CMB2 field args array.
	 * @param  CMB2_Field $field      CMB2 Field object.
	 * @return mixed
	 */
	public function _default_field_cb( $field_args, $field ) {
		return isset( $this->_instance[ $field->args( '_original_id' ) ] )
			? $this->_instance[ $field->args( '_original_id' ) ]
			: null;
	}
}
