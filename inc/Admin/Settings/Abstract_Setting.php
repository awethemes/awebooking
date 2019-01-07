<?php

namespace AweBooking\Admin\Settings;

use AweBooking\Plugin;
use AweBooking\Admin\Fluent_Settings;
use AweBooking\Component\Form\Form;
use WPLibs\Http\Request;
use Illuminate\Support\Arr;

abstract class Abstract_Setting extends Form implements Setting {
	/**
	 * The plugin instance.
	 *
	 * @var \AweBooking\Plugin
	 */
	protected $plugin;

	/**
	 * The setting label.
	 *
	 * @var string
	 */
	protected $label = '';

	/**
	 * The setting priority.
	 *
	 * @var string
	 */
	protected $priority = 55;

	/**
	 * Current section name in current request.
	 *
	 * @var string
	 */
	protected $current_section = '';

	/**
	 * Constructor.
	 *
	 * @param \AweBooking\Plugin $plugin The plugin instance.
	 */
	public function __construct( Plugin $plugin ) {
		$this->plugin = $plugin;

		$this->setup();

		parent::__construct( $this->get_id(), new Fluent_Settings( $plugin ), 'static' );
	}

	/**
	 * Setup the setting.
	 *
	 * @return void
	 */
	protected function setup() {}

	/**
	 * {@inheritdoc}
	 */
	public function get_id() {
		return $this->form_id;
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_label() {
		return $this->label;
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_priority() {
		return $this->priority;
	}

	/**
	 * {@inheritdoc}
	 */
	public function save( Request $request ) {
		$this->prepare_fields();

		// Get the fields.
		$fields = $this->prop( 'fields' );

		if ( ! empty( $this->sections ) ) {
			$_section = $request->get( '_section' );

			if ( ! array_key_exists( $_section, $this->sections ) ) {
				return false;
			}

			$fields = $this->sections[ $_section ]['fields'];
		}

		// Get the sanitized_values from request.
		$this->set_prop( 'fields', $fields );
		$raw_values = $this->get_sanitized_values( $request->post() );

		// Get the options.
		$options = cmb2_options( $this->plugin->get_current_option() );
		$original_options = cmb2_options( $this->plugin->get_original_option() );

		// Is translation?
		$is_translation = $this->current_screen_is_translation();

		// Loop over fields and perform the update option.
		// If some field missing from $values, we will fallback
		// fill by an empty string.
		foreach ( $fields as $key => $args ) {
			$key = $args['id'];

			// Ignore some non-savable fields.
			if ( 'title' === $args['type'] || 0 === strpos( '__', $key ) ) {
				continue;
			}

			// Sanitize value before save.
			$raw_value = array_key_exists( $key, $raw_values ) ? $raw_values[ $key ] : '';
			$value     = abrs_sanitize_option( $key, $raw_value );

			// Update the field value.
			if ( $is_translation && isset( $args['translatable'] ) && $args['translatable'] ) {
				$options->update( $key, $value, false, true );
			} else {
				$options->remove( $key, false );
				$original_options->update( $key, $value, false, true );
			}
		}

		// Save the options.
		$options->set();
		$original_options->set();

		return true;
	}

	/**
	 * {@inheritdoc}
	 */
	public function output( Request $request ) {
		$this->prepare_fields();

		// Get fields to prepare display.
		$fields = $this->prop( 'fields' );

		// Setup and print the sections.
		if ( $this->sections && $this->current_section ) {
			// Get only fields in current section to display.
			if ( array_key_exists( $this->current_section, $this->sections ) ) {
				$fields = $this->sections[ $this->current_section ]['fields'];
			}

			$this->output_nav_sections();
			echo '<input type="hidden" name="_section" value="' . esc_attr( $this->current_section ) . '" />';
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
	protected function output_nav_sections() {
		echo '<ul class="subsubsub">';

		foreach ( $this->sections as $id => $section ) {
			$id = sanitize_title( $id );

			if ( isset( $section->hidden ) && $section->hidden ) {
				continue;
			}

			// @codingStandardsIgnoreLine
			echo '<li><a href="' . esc_url( abrs_admin_route( '/settings', [ 'setting' => $this->get_id(), 'section' => $id ] ) ) . '" class="' . ( $this->current_section === $id ? 'current' : '' ) . '">' . esc_html( $section['title'] ) . '</a></li>';
		}

		echo '</ul><div class="clear"></div>';
	}

	/**
	 * Is current screen is a translation.
	 *
	 * @return bool
	 */
	protected function current_screen_is_translation() {
		return $this->plugin->get_current_option() !== $this->plugin->get_original_option() && abrs_running_on_multilanguage();
	}

	/**
	 * {@inheritdoc}
	 */
	public function prepare_fields() {
		$this->maybe_sets_translatable();

		parent::prepare_fields();

		if ( empty( $this->sections ) ) {
			return;
		}

		$this->current_section = abrs_http_request()->get( 'section',
			Arr::first( array_keys( $this->sections ) )
		);
	}

	/**
	 * Perform set translatable on each field.
	 *
	 * @return void
	 */
	protected function maybe_sets_translatable() {
		$translatable_fields = abrs_get_translatable_options();

		$fields = $this->prop( 'fields' );

		foreach ( $fields as &$args ) {
			if ( in_array( $args['id'], $translatable_fields ) ) {
				$args['translatable'] = true;
			}
		}

		unset( $args );
		$this->set_prop( 'fields', $fields );
	}
}
