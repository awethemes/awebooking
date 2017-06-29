<?php
namespace Skeleton;

class Widget extends \WP_Widget {
	/**
	 * Array of widget fields args.
	 *
	 * @var array
	 */
	public $fields = array();

	/**
	 * Array of default values for widget settings.
	 *
	 * @var array
	 */
	public $defaults = array();

	/**
	 * Store the instance properties as property.
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

		// Register widget update form fields.
		$this->fields = $this->fields();

		// Hooks some where to delete widget cache.
		add_action( 'save_post',    array( $this, '_flush_widget_cache' ) );
		add_action( 'deleted_post', array( $this, '_flush_widget_cache' ) );
		add_action( 'switch_theme', array( $this, '_flush_widget_cache' ) );
	}

	/**
	 * Front-end display of widget.
	 *
	 * @param  array $args      The widget arguments set up when a sidebar is registered.
	 * @param  array $instance  The widget settings as set by user.
	 */
	public function widget( $args, $instance ) {
		/** This filter is documented in wp-includes/widgets/class-wp-widget-pages.php */
		$title = apply_filters( 'widget_title', empty( $instance['title'] ) ? __( 'Categories' ) : $instance['title'], $instance, $this->id_base );

		var_dump($this->defaults);
	}

	/**
	 * Array of widget fields args.
	 *
	 * @var array
	 */
	public function fields() {
		return array();
	}

	/**
	 * Update form values as they are saved.
	 *
	 * @param  array $new_instance New settings for this instance as input by the user.
	 * @param  array $old_instance Old settings for this instance.
	 * @return array
	 */
	public function update( $new_instance, $old_instance ) {
		$this->_flush_widget_cache();
		// return $this->cmb2( true )->get_sanitized_values( $new_instance );
		return $new_instance;
	}

	/**
	 * Delete this widget's cache.
	 */
	public function _flush_widget_cache() {
		wp_cache_delete( $this->id, 'widget' );
	}

	/**
	 * Outputs the settings update form.
	 *
	 * @param array $instance Current settings.
	 */
	public function form( $instance ) {
		$cmb2 = $this->cmb2();
		$cmb2->object_id( $this->option_name );

		$this->_instance = wp_parse_args( (array) $instance, $this->defaults );
		$cmb2->show_form();
	}

	/**
	 * Creates a new instance of CMB2 and adds some fields.
	 *
	 * @return CMB2
	 */
	public function cmb2( $saving = false ) {
		$cmb2 = new \CMB2( array(
			'id'      => $this->option_name . '_box', // Option name is taken from the WP_Widget class.
			'hookup'  => false,
			'show_on' => array(
				'key'   => 'options-page', // Tells CMB2 to handle this as an option.
				'value' => array( $this->option_name ),
			),
		), $this->option_name );

		foreach ( $this->fields as $field ) {
			// Cache original field ID.
			$field['_original_id'] = $field['id'];

			// Because we disable hookup and CMB2 can't get multidimensional data,
			// so we need set default/value field by manually.
			if ( isset( $field['default'] ) ) {
				$field['_original_default'] = $field['default'];

				// A fallback to setting defaults property.
				if ( ! isset( $this->defaults[ $field['_original_id'] ] ) ) {
					$this->defaults[ $field['_original_id'] ] = $field['_original_default'];
				}

				// Never setting a default value in field.
				$field['default'] = null;
			}

			// And setting default callback.
			$field['default_cb'] = array( $this, '_field_default_callback' );

			// Re-setting field ID with widet ID style
			// if CMB2 show on widget update form.
			if ( ! $saving ) {
				$field['id'] = $this->get_field_name( $field['id'] );
			}

			$cmb2->add_field( $field );
		}

		return $cmb2;
	}

	/**
	 * Sets the field default, or the field value.
	 *
	 * @param  array       $field_args CMB2 field args array.
	 * @param  \CMB2_Field $field      CMB2 Field object.
	 * @return mixed
	 */
	public function _field_default_callback( $field_args, $field ) {
		return isset( $this->_instance[ $field->args( '_original_id' ) ] )
			? $this->_instance[ $field->args( '_original_id' ) ]
			: null ;
	}
}
