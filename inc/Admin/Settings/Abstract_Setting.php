<?php
namespace AweBooking\Admin\Settings;

use Illuminate\Support\Arr;
use Awethemes\Http\Request;
use AweBooking\Component\Form\Form_Builder;

abstract class Abstract_Setting extends Form_Builder implements Setting_Interface {
	/**
	 * The name for option key storing setting.
	 *
	 * @var string
	 */
	protected $option_key;

	/**
	 * Current section name in current request.
	 *
	 * @var string
	 */
	protected $current_section = '';

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->option_key = awebooking()->get_option_key();

		parent::__construct( $this->form_id, $this->option_key, 'options-page' );

		// Force the CMB2 on 'options-page'.
		$this->object_type( 'options-page' );
	}

	/**
	 * Get the setting ID.
	 *
	 * @return string
	 */
	public function get_id() {
		return $this->form_id;
	}

	/**
	 * Get the setting label.
	 *
	 * @return string
	 */
	public function get_label() {
		return '';
	}

	/**
	 * Output this setting.
	 *
	 * @param  \Awethemes\Http\Request $request The HTTP request.
	 * @return void
	 */
	public function output( Request $request ) {
		$this->prepare_fields();

		// Get fields to prepare display.
		$fields = $this->prop( 'fields' );

		// Setup and print the sections.
		if ( ! empty( $this->sections ) ) {
			$this->current_section = $request->get( 'section',
				Arr::first( array_keys( $this->sections ) )
			);

			// Get only fields in current section to display.
			if ( array_key_exists( $this->current_section, $this->sections ) ) {
				$fields = $this->sections[ $this->current_section ]['fields'];
			}

			$this->output_sections();
		}

		// Print the fields.
		echo '<div class="cmb2-wrap awebooking-wrap"><div class="cmb2-metabox">';
		foreach ( $fields as $field_args ) {
			$this->render_field( $field_args );
		}

		echo '</div></div>';
	}

	/**
	 * Output sections.
	 *
	 * @return void
	 */
	protected function output_sections() {
		echo '<ul class="subsubsub">';

		foreach ( $this->sections as $id => $section ) {
			$id = sanitize_title( $id );
			// @codingStandardsIgnoreLine
			echo '<li><a href="' . esc_url( abrs_admin_route( '/settings', [ 'setting' => $this->get_id(), 'section' => $id ] ) ) . '" class="' . ( $this->current_section == $id ? 'current' : '' ) . '">' . esc_html( $section['title'] ) . '</a></li>';
		}

		echo '</ul><div class="clear"></div>';
		echo '<input type="hidden" name="_section" value="' . esc_attr( $this->current_section ) . '" />';
	}

	/**
	 * Perform save setting.
	 *
	 * @param  \Awethemes\Http\Request $request The HTTP request.
	 * @return bool
	 */
	public function save( Request $request ) {
		// Get the options.
		$options = cmb2_options( $this->option_key );

		// Get the sanitized_values from request.
		$raw_values = $this->get_sanitized_values( $request->post() );

		// Get the fields.
		$fields = $this->prop( 'fields' );

		if ( ! empty( $this->sections ) ) {
			$this->prepare_fields();

			$_section = $request->get( '_section' );
			if ( ! array_key_exists( $_section, $this->sections ) ) {
				return false;
			}

			$fields = $this->sections[ $_section ]['fields'];
		}

		// Loop over fields and perform the update option.
		// If some field missing from $values, we will fallback
		// fill by an empty string.
		foreach ( $fields as $key => $args ) {
			$key = $args['id'];

			// Ignore some non-savable fields.
			if ( in_array( $args['type'], [ 'title', 'include' ] ) || 0 === strpos( '__', $key ) ) {
				continue;
			}

			$raw_value = array_key_exists( $key, $raw_values ) ? $raw_values[ $key ] : '';

			// Sanitize field value based on field type..
			$value = $this->sanitize_field_value( $args['type'], $raw_value );

			// Filter to sanitize the field value.
			$value = apply_filters( 'awebooking/sanitize_admin_settings_field', $value, $key, $raw_value );

			// Ignore update NULL value.
			if ( is_null( $value ) ) {
				continue;
			}

			// Update the field value.
			$options->update( $key, $value, false, true );
		}

		// Save the options.
		return $options->set();
	}

	/**
	 * Re-sanitize field value by based on field type.
	 *
	 * @param  string $type  The field type.
	 * @param  mixed  $value The mixed field value.
	 * @return mixed
	 */
	protected function sanitize_field_value( $type, $value ) {
		switch ( $type ) {
			case 'toggle':
			case 'checkbox':
				return abrs_sanitize_checkbox( $value );
			case 'texarea':
				return wp_kses_post( trim( $value ) );
			default:
				return abrs_recursive_sanitizer( $value, 'sanitize_text_field' );
		}
	}
}
