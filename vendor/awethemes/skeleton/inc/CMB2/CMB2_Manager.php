<?php
namespace Skeleton\CMB2;

use Skeleton\Container\Container;
use Skeleton\CMB2\Fields\Field_Interface;

class CMB2_Manager {
	/**
	 * Skeleton Container instance class.
	 *
	 * @var Container
	 */
	protected $container;

	/**
	 * A list of registered custom fields.
	 *
	 * @var array
	 */
	protected $registered_fields;

	/**
	 * Init CMB2_Manager
	 *
	 * @param Container $container
	 */
	public function __construct( Container $container ) {
		$this->container = $container;

		$this->registered_fields = apply_filters( 'skeleton/cmb2/fields', array(
			'icon'              => 'Skeleton\CMB2\Fields\Icon_Field',
			'range'             => 'Skeleton\CMB2\Fields\Range_Field',
			'toggle'            => 'Skeleton\CMB2\Fields\Toggle_Field',
			'backups'           => 'Skeleton\CMB2\Fields\Backups_Field',
			'js_code'           => 'Skeleton\CMB2\Fields\JS_Code_Field',
			'css_code'          => 'Skeleton\CMB2\Fields\CSS_Code_Field',
			'html_code'         => 'Skeleton\CMB2\Fields\HTML_Code_Field',
			'image'             => 'Skeleton\CMB2\Fields\Image_Field',
			'radio_image'       => 'Skeleton\CMB2\Fields\Radio_Image_Field',
			'link_color'        => 'Skeleton\CMB2\Fields\Link_Color_Field',
			'rgba_colorpicker'  => 'Skeleton\CMB2\Fields\Rgba_Colorpicker_Field',
			'typography'        => 'Skeleton\CMB2\Fields\Typography_Field',
		));
	}

	/**
	 * Register a custom field.
	 *
	 * @param  string  $name       Custom field name.
	 * @param  string  $class_name Custom field class name.
	 * @param  boolean $force      Force register custom field even already exists.
	 * @return bool
	 */
	public function register_field( $name, $class_name, $force = false ) {
		if ( ! $force && $this->has_registered_field( $name ) ) {
			return false;
		}

		$this->registered_fields[ $name ] = $class_name;
		return true;
	}

	/**
	 * Check if a custom field already in registered list.
	 *
	 * @param  string $name Field name.
	 * @return boolean
	 */
	public function has_registered_field( $name ) {
		return isset( $this->registered_fields[ $name ] );
	}

	/**
	 * Return a array custom registered fields.
	 *
	 * @return array
	 */
	public function get_registered_fields() {
		return $this->registered_fields;
	}

	/**
	 * Hooks custom fields to CMB2.
	 */
	public function hooks_fields() {
		foreach ( $this->registered_fields as $type => $class ) {
			$field = new $class( $this->container, $type );
			if ( ! $field instanceof Field_Interface ) {
				return;
			}

			// Hook current field to CMB2.
			add_action( 'cmb2_render_' . $type, array( $field, 'output' ), 10, 5 );
			add_action( 'cmb2_sanitize_' . $type, array( $field, 'sanitization' ), 10, 5 );

			// If custom field tell that is not repeatable, add this field to blacklist.
			if ( ! $field->repeatable ) {
				add_filter( 'cmb2_non_repeatable_fields', array( $field, 'disable_repeatable' ) );
			}

			// Run custom hooks after done.
			$field->hooks();
		}
	}
}
