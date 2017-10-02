<?php
namespace AweBooking\Admin;

use Skeleton\Admin_Page;
use Skeleton\CMB2\Panel;
use Skeleton\CMB2\Section;
use AweBooking\Admin\Fields\Field_Proxy;
use AweBooking\Admin\Settings\Email_Setting;
use AweBooking\Admin\Settings\General_Setting;
use AweBooking\Admin\Settings\Display_Setting;

class Admin_Settings extends Admin_Page {
	/**
	 * Make a new page settings.
	 */
	public function __construct() {
		parent::__construct( awebooking( 'option_key' ) );

		$this->strings = array(
			'updated' => esc_html__( 'Your settings have been saved.', 'awebooking' ),
		);

		// Register core settings.
		$this->core_settings();

		/**
		 * Here you can register or custom AweBooking settings.
		 *
		 * @param Admin_Settings $settings Admin_Setting object instance.
		 */
		do_action( 'awebooking/register_admin_settings', $this );

		$this->set(array(
			'menu_slug'  => 'awebooking-settings',
			'menu_title' => esc_html__( 'Settings', 'awebooking' ),
		));
	}

	/**
	 * Register core fields.
	 *
	 * @return void
	 */
	protected function core_settings() {
		new General_Setting( $this );
		new Display_Setting( $this );
		new Email_Setting( $this );

		$this->add_section( 'backup', [
			'title'    => esc_html__( 'Backups', 'awebooking' ),
			'priority' => 1994,
		])
		->add_field([
			'id'   => 'backups',
			'type' => 'backups',
		]);

		// Deprecated hooks.
		// TODO: Remove this in final version.
		do_action( 'awebooking/admin_settings/register', $this );
	}

	/**
	 * Init page hooks.
	 *
	 * Overwrite `parent::init()` method.
	 */
	public function init() {
		add_action( 'admin_init', array( $this, 'register_settings' ) );

		awebooking( 'admin_menu' )->add_submenu( $this->menu_slug, array(
			'page_title'  => $this->page_title,
			'menu_title'  => $this->menu_title,
			'function'    => $this->render_callback,
		));

		// Hook in our save notices.
		add_action( "cmb2_save_options-page_fields_{$this->prop( 'id' )}", array( $this, 'settings_notices' ), 10, 3 );
	}

	/**
	 * Get a field object.
	 *
	 * @param  mixed           $field The field id or field config array or CMB2_Field object.
	 * @param  CMB2_Field|null $group Optional, CMB2_Field object (group parent).
	 * @return Field_Proxy|null
	 */
	public function get_field( $field, $group = null ) {
		$field = parent::get_field( $field, $group );

		return $field ? new Field_Proxy( $this, $field ) : null;
	}

	/**
	 * Loops through and saves field data.
	 *
	 * Overwrite `parent::save_field` method.
	 *
	 * @param  int    $object_id    Object ID.
	 * @param  string $object_type  Type of object being saved. (e.g., post, user, or comment).
	 * @param  array  $data_to_save Array of key => value data for saving. Likely $_POST data.
	 *
	 * @return CMB2
	 */
	public function _save_fields( $object_id = 0, $object_type = '', $data_to_save = [] ) {
		// CMB2 required all input data of fields must be present,
		// so we need fake missing input data then inject to $data_to_save.
		return parent::save_fields( $object_id, $object_type, $data_to_save );
	}

	/**
	 * Loops through and displays fields.
	 *
	 * TODO: ...
	 *
	 * Overwrite `parent::show_form()` method.
	 *
	 * @param int    $object_id   Object ID.
	 * @param string $object_type Type of object being saved. (e.g., post, user, or comment).
	 */
	public function _show_form( $object_id = 0, $object_type = '' ) {
		// Prepare fields.
		$this->prepare_controls();
		$this->prepare_validate();

		// Prints the fields.
		$this->render_form_open( $object_id, $object_type );
		$this->render_fields();
		$this->render_form_close( $object_id, $object_type );
	}

	/**
	 * Build and render fields.
	 *
	 * @return void
	 */
	protected function render_fields() {
		list( $section, $panel ) = $this->get_request_section();

		// Build main navigation.
		$request_tab = is_null( $panel ) ? $section : $panel;
		$navigation  = [];

		foreach ( $this->tabs() as $tabable ) {
			$navigation[] = sprintf( '<a href="%1$s" class="nav-tab%3$s">%4$s %2$s</a>',
				esc_url( add_query_arg( 'tab', $tabable->id, admin_url( "admin.php?page={$this->menu_slug}" ) ) ),
				esc_html( $tabable->title ?: $tabable->id ),
				esc_attr( $request_tab && $request_tab->id === $tabable->id ? ' nav-tab-active' : '' ),
				$tabable->build_icon()
			);
		}

		// Prints the navigation.
		print( "\n<nav class=\"nav-tab-wrapper awebooking-nav-tab-wrapper\">\n\t" . implode( $navigation, "\n\t" ) . "\n</nav>\n" );

		// Prevent if request tab is invalid.
		if ( is_null( $request_tab ) ) {
			return;
		}

		// If not in panel, render the section.
		if ( is_null( $panel ) ) {
			$this->render_section( $section );
			return;
		}

		// Build panel navigation.
		$navigation_panel = [];
		foreach ( $panel->sections as $_section ) {
			$navigation_panel[] = sprintf( '<li><a href="%1$s" class="%3$s">%2$s</a></li>',
				esc_url( add_query_arg( [ 'tab' => $panel->id, 'section' => $_section->id ], admin_url( "admin.php?page={$this->menu_slug}" ) ) ),
				esc_html( $_section->title ?: $_section->id ),
				esc_attr( $section->id === $_section->id ? 'current' : '' )
			);
		}

		// Prints the navigation.
		echo "\n<ul class=\"subsubsub\">\n\t" . implode( $navigation_panel, "\n\t" ) . "\n</ul>";
		echo "\n<div class=\"clear\"></div>\n";

		$this->render_section( $section );
	}

	/**
	 * Render CMB2 section.
	 *
	 * @param  Section $section Section instance.
	 * @return void
	 */
	protected function render_section( Section $section ) {
		printf( '<div id="%1$s" class="cmb2-tab-pane active">', esc_attr( $section->uniqid() ) );

		// Loop through section fields and render each field.
		foreach ( $section->fields as $field ) {
			$this->render_field( $field );
		}

		print "\n</div>\n";
	}

	/**
	 * Get working section from request.
	 *
	 * @return array|null
	 */
	protected function get_request_section() {
		$tabs = $this->tabs();

		$request_tab = isset( $_REQUEST['tab'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['tab'] ) ) : 'general';
		$request_tab = isset( $tabs[ $request_tab ] ) ? $tabs[ $request_tab ] : null;

		if ( $request_tab instanceof Section ) {
			return [ $request_tab, null ];
		}

		if ( $request_tab instanceof Panel ) {
			$panel = $request_tab;

			if ( ! empty( $_REQUEST['section'] ) ) {
				$request_section = sanitize_text_field( wp_unslash( $_REQUEST['section'] ) );
			} else {
				$flat_sections   = array_keys( $panel->sections );
				$request_section = isset( $flat_sections[0] ) ? $flat_sections[0] : null;
			}

			// Don't return any if request a invalid section.
			if ( empty( $panel->sections[ $request_section ] ) ) {
				return;
			}

			return [ $panel->sections[ $request_section ], $panel ];
		}
	}
}
